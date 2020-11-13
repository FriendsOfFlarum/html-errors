import {extend} from 'flarum/extend';
import app from 'flarum/app';
import ErrorSettingsModal from './components/ErrorSettingsModal';

app.initializers.add('fof-html-errors', app => {
    app.extensionSettings['fof-html-errors'] = () => app.modal.show(ErrorSettingsModal);
});
