import Controller from '@ember/controller';
import { inject as service } from '@ember/service';

export default Controller.extend({
    notifications: service('notifications'),

    actions: {
        submit() {
            this.get('model').save().then(() => {
                this.get('notifications').add('Cliente foi criado', 'user');
                this.transitionToRoute('clientes');
            }).catch((adapterError) => {

                let object = adapterError.errors[0];
                let erroMsg = "";
                for (var property in object) {
                    if (object.hasOwnProperty(property)) {
                        erroMsg += property + " " + object[property];
                    }
                }
                this.set('erro', erroMsg);
                
            });
        }
    }
});
