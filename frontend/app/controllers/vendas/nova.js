import Controller from '@ember/controller';
import { inject as service } from '@ember/service';

export default Controller.extend({
    notifications: service('notifications'),
    
    actions: {
        submit() {
            this.get('model').save().then(() => {
                this.get('notifications').add('A venda foi criada', 'building');
                this.transitionToRoute('vendas');
            });
        }
    }
});
