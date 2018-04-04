import {extend} from 'flarum/extend';
import app from 'flarum/app';
import ErrorSettingsModal from 'flagrow/html-errors/components/ErrorSettingsModal';

app.initializers.add('flagrow-html-errors', app => {
    app.extensionSettings['flagrow-html-errors'] = () => app.modal.show(new ErrorSettingsModal());
});
