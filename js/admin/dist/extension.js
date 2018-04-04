'use strict';

System.register('flagrow/html-errors/components/ErrorSettingsModal', ['flarum/app', 'flarum/components/SettingsModal'], function (_export, _context) {
    "use strict";

    var app, SettingsModal, settingsPrefix, translationPrefix, ErrorSettingsModal;
    return {
        setters: [function (_flarumApp) {
            app = _flarumApp.default;
        }, function (_flarumComponentsSettingsModal) {
            SettingsModal = _flarumComponentsSettingsModal.default;
        }],
        execute: function () {
            settingsPrefix = 'flagrow-html-errors.';
            translationPrefix = 'flagrow-html-errors.admin.settings.';

            ErrorSettingsModal = function (_SettingsModal) {
                babelHelpers.inherits(ErrorSettingsModal, _SettingsModal);

                function ErrorSettingsModal() {
                    babelHelpers.classCallCheck(this, ErrorSettingsModal);
                    return babelHelpers.possibleConstructorReturn(this, (ErrorSettingsModal.__proto__ || Object.getPrototypeOf(ErrorSettingsModal)).apply(this, arguments));
                }

                babelHelpers.createClass(ErrorSettingsModal, [{
                    key: 'title',
                    value: function title() {
                        return app.translator.trans(translationPrefix + 'title');
                    }
                }, {
                    key: 'className',
                    value: function className() {
                        return 'Modal--large';
                    }
                }, {
                    key: 'form',
                    value: function form() {
                        var _this2 = this;

                        return [403, 404, 500, 503].map(function (error) {
                            return m('.Form-group', [m('label', app.translator.trans(translationPrefix + 'error.' + error)), m('textarea.FormControl', {
                                bidi: _this2.setting(settingsPrefix + 'custom' + error + 'ErrorHtml'),
                                placeholder: app.translator.trans(translationPrefix + 'placeholder.empty_for_default')
                            })]);
                        });
                    }
                }]);
                return ErrorSettingsModal;
            }(SettingsModal);

            _export('default', ErrorSettingsModal);
        }
    };
});;
'use strict';

System.register('flagrow/html-errors/main', ['flarum/extend', 'flarum/app', 'flagrow/html-errors/components/ErrorSettingsModal'], function (_export, _context) {
    "use strict";

    var extend, app, ErrorSettingsModal;
    return {
        setters: [function (_flarumExtend) {
            extend = _flarumExtend.extend;
        }, function (_flarumApp) {
            app = _flarumApp.default;
        }, function (_flagrowHtmlErrorsComponentsErrorSettingsModal) {
            ErrorSettingsModal = _flagrowHtmlErrorsComponentsErrorSettingsModal.default;
        }],
        execute: function () {

            app.initializers.add('flagrow-html-errors', function (app) {
                app.extensionSettings['flagrow-html-errors'] = function () {
                    return app.modal.show(new ErrorSettingsModal());
                };
            });
        }
    };
});