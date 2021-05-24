/**
 * Created by:  Pavel Kondratenko.
 * Created at:  03.05.14 20:19
 * Email:       gustarus@gmail.com
 * Web:         http://webulla.ru
 */

(function ($) {
    // опции по умолчанию
    var defaults = {
        latestIndex: 0,
        relationFormName: undefined,
        selectionColumn: undefined,
        emptyText: undefined,
        emptyTextOptions: undefined,
        rowTemplate: undefined,
    };

    // актуальные индексы для добавления строк
    // для каждого класса модели создается свой индекс
    // актуально если на одной странице используется
    // несколько activeGrid для одной и той же модели
    var indexes = {};


    /**
     * Плагин.
     * @param method
     * @returns {*}
     */
    $.fn.activeGrid = function (method) {
        // получаем модель таблицы
        var model;
        if (typeof method == 'undefined' || typeof method == 'object') {
            model = new window.activeGrid($.extend({el: this}, method || {}));
            this.data('WblGrid', model);
        } else if (helpers[method]) {
            helpers[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            model = this.data('WblGrid');
            if (!model) {
                $.error('[jQuery.activeGrid] Model activeGrid does not exist in $.data.');
            } else if (!model[method]) {
                $.error('[jQuery.activeGrid] Method ' + method + ' does not exist in activeGrid model.');
            } else {
                return model[method].apply(model, Array.prototype.slice.call(arguments, 1));
            }
        }

        return this;
    };


    /**
     * Методы отсюда используются для
     * настройки глобального объекта activeGrid извне.
     */
    var helpers = {
        setLatestIndex(relationFormName, index) {
            indexes[relationFormName] = parseInt(index);
        },

        getLatestIndex(relationFormName) {
            return indexes[relationFormName] || 0;
        },
    };


    /**
     * Модель таблицы.
     * @param options
     * @constructor
     */
    window.activeGrid = function (options) {
        var that = this;

        this.el = null;
        this.$el = null;
        this.$add = null;
        this.$delete = null;
        this.$controls = $();

        /**
         * Вызывается в конце создания модели.
         */
        this.setup = function () {
            // установка элемента таблицы
            if (options.el) {
                that.$el = options.el instanceof $ ? options.el : $(options.el);
                that.el = that.$el[0];
                delete (options.el);
            }

            // заглушка для пустой таблицы
            that.$empty = $('<tr><td colspan="' + that.getColsCount() + '">' + options.emptyText + '</td></tr>');
            that.$empty.attr(options.emptyTextOptions).hide();
            that.addHeadRow(that.$empty);

            // создание индекса для строк
            if (options.latestIndex) {
                indexes[options.relationFormName] = options.latestIndex;
            } else {
                indexes[options.relationFormName] = indexes[options.relationFormName] || 0;
            }

            that.options = options;

            that.refresh();
        };


        /**
         * @returns {Grid}
         */
        this.getModel = function () {
            return that;
        };

        /**
         * Инкрементирует индекс модели и возвращает новое значение.
         */
        this.addIndex = function () {
            indexes[options.relationFormName]++;
            return indexes[options.relationFormName];
        };


        /**
         * Возвращает бокс из шапки таблицы.
         */
        this.getHeaderSelectionBox = function () {
            return that.$el.find('th ' + that.options.checkboxes);
        };

        /**
         * Возвращает бокс из тела таблицы.
         */
        this.getSelectionBox = function () {
            return that.$el.find('td ' + that.options.checkboxes);
        };

        /**
         * Возвращает количество выделенных строк.
         */
        this.getSelectionBoxChecked = function () {
            // находим все боксы
            var $boxes = that.getSelectionBox();

            // получаем информацию о состоянии чекбоксов
            return $boxes.filter(':checked');
        };

        this.unsetSelectionBox = function () {
            var $rows = that.getSelectedRows();
            var $header = that.getHeaderSelectionBox();
            var $boxes = that.getSelectionBox();

            $header.prop('checked', false);
            $boxes.prop('checked', false);

            $rows.each(function () {
                this.removeAttribute('data-selected');
            });

            that.refresh();
        };


        /**
         * Добавляет строку в шапку.
         * @param row
         */
        this.addHeadRow = function (row) {
            that.$el.find('thead').append(row);
        };

        /**
         * Получает строки из шапки.
         * @returns {*}
         */
        this.getHeadRows = function () {
            return that.$el.find('thead tr');
        };


        /**
         * Добавляет строку в тело.
         * @param row
         */
        this.addRow = function (row) {
            that.$el.find('tbody').append(row);

            // инициализация необходимой валидации
            // TODO Инициализация валидации на только что добавленных полях.
            // if (that.$el.yiiActiveForm) {
            // 	var $fields = $row.find('input, select');
            // 	$fields.each(function() {
            // 		var $field = $(this);
            // 		that.$el.yiiActiveForm('add', $field.attr('name'));
            // 	});
            // }
        };

        /**
         * Возвращает строки из тела.
         * @returns {*}
         */
        this.getRows = function () {
            return that.$el.find('tbody tr');
        };

        this.bindControl = function (dom, name) {
            switch (name) {
                case 'add':
                    that.bindAddControl(dom);
                    break;

                case 'delete':
                    that.bindDeleteControl(dom);
                    break;

                default:
                    that.bindAdditionalControl(dom);
            }
        };

        /**
         * Привязывает кнопку "Добавить".
         * @param dom
         */
        this.bindAddControl = function (dom) {
            that.$add = $(dom);

            // [событие] добавление строки
            that.$add.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var index = that.addIndex();
                var $row = $(that.options.rowTemplate.replace(/#index#/g, index));

                that.addRow($row);
                that.$el.trigger('active-grid:add', $row);

                that.refresh();
            });

            that.$controls = that.$controls
                .add(that.$add);
            that.refreshControls();
        };

        /**
         * Привязывает кнопку "Удалить".
         * @param dom
         */
        this.bindDeleteControl = function (dom) {
            that.$delete = $(dom);

            // [событие] удаление строк
            that.$delete.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var $rows = that.getSelectedRows();

                $rows.remove();
                that.$el.trigger('active-grid:remove', $rows);

                that.refresh();
            });

            that.$controls = that.$controls
                .add(that.$delete);
            that.refreshControls();
        };

        /**
         * Привязывает другую кнопку.
         * Позволяет добавить дополнительные кнопки которые действуют как основные.
         * Дополнительные свойства у дополнительных кнопок:
         *  data-disabled - сделать кнопку disabled
         *  data-enable-on-select - включать кнопку по выделению одного или более элементов
         * @param dom
         */
        this.bindAdditionalControl = function (dom) {
            var $control = $(dom);

            // disable needed attributes
            if ($control.is('[data-disabled]')) {
                $control.attr('disabled', true);
            }

            // bind on click event when needed
            if ($control.is('[data-click]')) {
                var callback = new Function('e',
                    'e.preventDefault();'
                    + 'var $grid = $("#' + that.$el.attr('id') + '");'
                    + $control.attr('data-click')
                );

                $control.on('click', callback);
            }

            that.$controls = that.$controls
                .add($control);
        };


        /**
         * Обновляем таблицу.
         */
        this.refresh = function () {
            that.refreshEmptyContent();
            that.refreshHeaderCheckbox();
            that.refreshControls();
        };

        this.refreshEmptyContent = function () {
            // проверяем наличие строк
            that.getRows().length
                ? that.$empty.hide()
                : that.$empty.show();
        };

        this.refreshHeaderCheckbox = function () {
            // обновляем состояние бокса в шапке
            var $boxes = that.getSelectionBox();
            var $checked = that.getSelectionBoxChecked();
            that.$box && that.$box.prop('checked', $boxes.length === $checked.length);
        };

        this.refreshControls = function () {
            // получаем все кнопки которые должны следить
            // за состоянием выделения строк
            var $delete = that.$delete;
            var $controls = that.$controls.filter('[data-enable-on-select]');
            var $tosync = $([]).add($delete).add($controls);

            if ($tosync.length) {
                // устанавливаем состояние кнопок которым это необходимо
                var $checked = that.getSelectionBoxChecked();
                $tosync.attr('disabled', !$checked.length);
            }
        };

        /**
         * Возвращает количество строк.
         * @returns {int}
         */
        this.getColsCount = function () {
            return that.getHeadRows().first().find('td, th').length;
        };

        /**
         * Возвращает выбранные строки.
         * @returns {*}
         */
        this.getSelectedRows = function () {
            return that.getSelectionBox().filter(':checked').closest('tr');
        };

        /**
         * Установка колонки для выбора строк.
         * @param name
         */
        this.setSelectionColumn = function (name) {
            // селектор для поиска чекбоксов выбора строки
            that.options.checkboxes = 'input[name="' + name + '"]';

            // находим чекбокс в шапке
            that.$box = that.getHeaderSelectionBox();

            // [событие] клик по чекбоксу в шапке
            that.$box.on('change', function () {
                that.getSelectionBox().prop('checked', that.$box.prop('checked')).trigger('change');

                that.refreshControls();
            });

            // [событие] клик по чекбоксу
            that.$el.on('change', 'td ' + that.options.checkboxes, function () {
                var $box = $(this);
                var $row = $box.closest('tr');

                $box.prop('checked')
                    ? $row.get(0).setAttribute('data-selected', true)
                    : $row.get(0).removeAttribute('data-selected');

                that.refreshHeaderCheckbox();
                that.refreshControls();
            });
        };


        this.setup();
    };
})(window.jQuery);
