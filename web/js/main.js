// Transaction model
function Transaction() {
    var self = this;

    self.id = ko.observable();
}

// Column model
function Column(data) {
    var self = this;

    self.name = ko.observable(data.name);
    self.label = ko.observable(data.label);
    self.type = ko.observable(data.type);
    self.choices = ko.observable(data.choices);
    self.required = data.required;

    // Convert choices for select display
    self.convertChoices = function () {
        if (self.choices === undefined) {
            return [];
        }

        var result = [{name: "Chose user", id: ""}];

        for (var key in self.choices()) {
            result.push({name: key, id: self.choices()[key]});
        }

        return result;
    };

    // Get title key for related object
    self.titleKey = function () {
        return 'name';
    };

    // Get id key for related object
    self.idKey = function () {
        return 'id';
    };
}

// Filter model
function Filter(data, column) {
    var self = this;

    self.name = ko.observable(data.name);
    self.type = ko.observable(data.type);
    self.value = ko.observable();
    self.column = column;
}

function DatagridViewModel() {
    var self = this;

    self.columns = ko.observableArray();
    self.filters = ko.observableArray();
    self.transactions = ko.observableArray();
    self.filteredTransactions = ko.observableArray();
    self.chosenTransaction = ko.observable();

    // Compute non-deleted transactions
    self.currentTransactions = ko.computed(function () {
        return self.filteredTransactions().length === 0 ? self.transactions() : self.filteredTransactions();
    });

    // Compute visible columns
    self.visibleColumns = ko.computed(function () {
        return ko.utils.arrayFilter(self.columns(), function (column) {
            return column.type() != 'hidden';
        });
    });

    // Compute current balance
    self.currentBalance = ko.computed(function () {
        var total = 0;

        for (var i = 0; i < self.currentTransactions().length; i++) {
            total += parseInt(self.currentTransactions()[i].sum());
        }

        return total;
    });

    // Load data from API
    self.loadData = function () {
        // Load transactions and fields
        $.get('/api/transactions', function (response) {
            var columns = [];
            var filters = [];

            for (var i = 0; i < response.columns.length; i++) {
                var item = response.columns[i];
                var column = new Column(item);

                columns.push(column);
                filters.push(new Filter(item, column));
            }

            self.columns(columns);
            self.filters(filters);

            self.transactions($.map(response.data, function (item) {
                return ko.mapping.fromJS(item, Transaction);
            }));

            self.chosenTransaction(null);
        });
    };

    // Show grid
    self.showGrid = function () {
        self.chosenTransaction(null);
    };


    // Get view template for transaction depends on field type
    self.getViewTemplate = function (field, bindingContext) {
        if (field.type() == 'choice') {
            return 'select-view-template';
        }

        return 'default-view-template';
    };

    // Get filter template for transaction depends on field type
    self.getFilterTemplate = function (field, bindingContext) {
        if (field.type() == 'choice') {
            return 'select-filter-template';
        }

        return 'default-filter-template';
    };

    // Detect which template we have to use for field.type
    self.getFormTemplate = function (field, bindingContext) {
        var template = "default-template";

        switch (field.type()) {
            case 'hidden':
                template = "hidden-template";
                break;
            case 'choice':
                template = "select-template";
                break;
            case 'number':
            case 'text':
                template = "input-template";
                break;
            case 'textarea':
                template = "textarea-template";
                break;
        }

        return template;
    };

    // Add a new transaction
    self.addTransaction = function () {
        var transaction = new Transaction();

        for (var i = 0; i < self.columns().length; i++) {
            if (self.columns()[i].type() == 'choice') {
                transaction[self.columns()[i].name()] = {id: ko.observable(null), name: ko.observable(null)};
            } else {
                transaction[self.columns()[i].name()] = ko.observable();
            }
        }

        self.chosenTransaction(transaction);
    };

    // Edit the transaction
    self.editTransaction = function (transaction) {
        self.chosenTransaction(ko.mapping.fromJS(ko.toJS(transaction)));
    };

    // Remove the transaction
    self.removeTransaction = function (transaction) {
        $.ajax({
            url: '/api/transactions/' + transaction.id(),
            type: 'DELETE'
        });

        self.transactions.remove(transaction);
    };

    // Save the transaction that was set in chosenTransaction
    self.saveTransaction = function () {
        var transaction = self.chosenTransaction();
        var validationModel = {};

        for (var i = 0; i < self.columns().length; i++) {
            var column = self.columns()[i];

            if (column.required || column.type() == 'number') {
                var value = typeof transaction[column.name()] == 'object' ? transaction[column.name()][column.idKey()]() : transaction[column.name()]();

                var requirements = {};

                if (column.required) {
                    requirements['required'] = true;
                }

                if (column.type() == 'number') {
                    requirements['digit'] = true;
                }

                validationModel[column.name()] = ko.observable(value).extend(requirements);
                console.log(value);
                console.log(ko.observable(value).extend(requirements).isValid());
            }
        }

        if (!ko.validatedObservable(validationModel).isValid()) {
            return false;
        }

        var data = ko.mapping.toJS(transaction);

        data.user = data.user.id;

        if (transaction.id === undefined || transaction.id() === undefined) {
            $.post('/api/transactions', data, function (response) {
                self.loadData();
            });
        } else {
            $.ajax({
                url: '/api/transactions/' + transaction.id(),
                type: 'PUT',
                data: data,
                success: function (response) {
                    self.loadData();
                }
            });
        }
    };

    // Append filters
    self.appendFilters = function () {
        var filtered = [];
        var isFiltered = false;

        for (var i = 0; i < self.filters().length; i++) {
            var filter = self.filters()[i];

            if (filter.value()) {
                isFiltered = true;

                if (filtered.length === 0) {
                    var data = self.transactions();
                } else {
                    var data = filtered;
                }

                data = ko.utils.arrayFilter(data, function (transaction) {
                    if (transaction[filter.name()] == undefined) {
                        return false;
                    }

                    var value = typeof transaction[filter.name()] == 'function' ? transaction[filter.name()]() : transaction[filter.name()];

                    if (filter.type() == 'choice') {
                        return value[filter.column.idKey()]() == filter.value();
                    }

                    return String(value).lastIndexOf(filter.value(), 0) === 0;
                });

                if (data != self.transactions()) {
                    filtered = data;
                }
            }
        }

        if (isFiltered) {
            self.filteredTransactions(filtered);
        } else {
            self.filteredTransactions([]);
        }
    };

    self.loadData();
}


ko.applyBindings(new DatagridViewModel());