import app from 'flarum/admin/app';

// Keeping the old flagrow settings prefix because it's easier
const settingsPrefix = 'flagrow-html-errors.';
const translationPrefix = 'fof-html-errors.admin.settings.';

app.initializers.add('fof-html-errors', (app) => {
    app.extensionData.for('fof-html-errors').registerSetting(function () {
        return [403, 404, 500, 503].map((error) => (
            <div className="Form-group">
                <label>{app.translator.trans(translationPrefix + 'error.' + error)}</label>
                <textarea
                    className="FormControl"
                    bidi={this.setting(settingsPrefix + 'custom' + error + 'ErrorHtml')}
                    placeholder={app.translator.trans(translationPrefix + 'placeholder.empty_for_default')}
                ></textarea>
            </div>
        ));
    });
});
