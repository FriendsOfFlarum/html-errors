import app from 'flarum/app';
import ErrorSettingsPage from './components/ErrorSettingsPage';

app.initializers.add('fof-html-errors', app => {
    app.extensionData.for('fof-html-errors').registerPage(ErrorSettingsPage);
});
