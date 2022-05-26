import app from 'flarum/admin/app';

// Keeping the old flagrow settings prefix because it's easier
const settingsPrefix = 'flagrow-html-errors.';
const translationPrefix = 'fof-html-errors.admin.settings.';

app.initializers.add('fof-html-errors', () => {
  const extensionData = app.extensionData.for('fof-html-errors');

  [403, 404, 500, 503].map((error) => {
    extensionData.registerSetting({
      setting: `${settingsPrefix}custom${error}ErrorHtml`,
      label: app.translator.trans(`${translationPrefix}error.${error}`),
      placeholder: app.translator.trans(`${translationPrefix}placeholder.empty_for_default`),
      type: 'textarea',
      rows: 10,
    });
  });
});
