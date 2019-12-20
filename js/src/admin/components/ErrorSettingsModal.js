import app from 'flarum/app';
import SettingsModal from 'flarum/components/SettingsModal';

// Keeping the old flagrow settings prefix because it's easier
const settingsPrefix = 'flagrow-html-errors.';
const translationPrefix = 'fof-html-errors.admin.settings.';

/* global m */

export default class ErrorSettingsModal extends SettingsModal {
    title() {
        return app.translator.trans(translationPrefix + 'title');
    }

    className() {
        return 'Modal--large';
    }

    form() {
        return [403, 404, 500, 503].map(
            error => m('.Form-group', [
                m('label', app.translator.trans(translationPrefix + 'error.' + error)),
                m('textarea.FormControl', {
                    bidi: this.setting(settingsPrefix + 'custom' + error + 'ErrorHtml'),
                    placeholder: app.translator.trans(translationPrefix + 'placeholder.empty_for_default'),
                }),
            ])
        );
    }
}
