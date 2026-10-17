/**
 * The entry of this module's Vue bundle (used verbatim by the core's `vue.build.mjs` instead of
 * the generated one): the "Send message" action on the cards of the core's People directory,
 * the entry `mail` of its extension slot `user.card-actions` (see "Card actions" in the core's
 * docs/develop/ui-js-vuejs-extensions.md).
 */
import { register, registerSlotComponent } from '@humhub/vue';
import MailPeopleCardAction from './components/MailPeopleCardAction.vue';

register('MailPeopleCardAction', MailPeopleCardAction);
registerSlotComponent('user.card-actions', 'MailPeopleCardAction', { id: 'mail', sortOrder: 300 });
