<template>
    <button
        v-if="state && state.canMessage && state.url"
        type="button"
        class="c-entity-card__action c-entity-card__action--icon"
        :class="buttons.mailClass || 'btn btn-light'"
        :title="label"
        :aria-label="label"
        @click="compose"
    ><i class="ti ti-mail" aria-hidden="true"></i></button>
</template>

<script>
/**
 * The "Send message" action on the cards of the core's People directory — the mail module's entry
 * `mail` (sortOrder 300) of the extension slot `user.card-actions` (see `vue/index.js` and the
 * user module's `PeopleCard`): an icon button opening the new-conversation form with the user
 * preselected in the global modal. The bundle is only registered on the page for a viewer who
 * may start conversations (`Events::onPeopleDirectoryInit()`).
 *
 * Whether the viewer may write to the user comes from `GET /api/v2/mail/recipient-states`, not
 * with the user: the cards of one render ask together, in a single request for all of their
 * users (at most `MAX_IDS` per request), and a user is asked for once per page load. Nothing is
 * rendered while the request is on its way, when it fails, or for a user who does not accept
 * messages from the viewer. The translations of `MailModule.base` load along with the first
 * request — a slot component's own categories are not preloaded by the island.
 *
 * The button classes are `buttons.mailClass` of the slot's context (a theme may add it to
 * `PeopleDirectory::$buttonClasses`), else `btn btn-light`.
 *
 * @since 3.5.0
 */
import { apiUrl, client, i18n, log, modal } from '@humhub/vue';
import { reactive } from 'vue';

// What the endpoint answers at most per request (RecipientStateService::MAX_IDS).
const MAX_IDS = 100;

// userId -> {canMessage, url} | null (nothing to offer, or not loaded yet), shared by every card of the page.
const states = reactive({});
let queued = new Set();
let flushScheduled = false;
let translations = null;

const flush = () => {
    flushScheduled = false;
    const ids = [...queued];
    queued = new Set();

    translations ??= i18n.preload(['MailModule.base']).catch(() => undefined);

    for (let i = 0; i < ids.length; i += MAX_IDS) {
        const chunk = ids.slice(i, i + MAX_IDS);
        Promise.all([
            client.get(apiUrl('mail/recipient-states', { ids: chunk.join(',') })),
            translations,
        ]).then(([response]) => {
            const results = (response && response.results) || {};
            chunk.forEach((id) => {
                states[id] = results[id] || null;
            });
        }).catch((error) => {
            log.error('Could not load whether the users can be messaged', error);
        });
    }
};

const request = (userId) => {
    if (userId in states || queued.has(userId)) {
        return;
    }

    // Marks it as asked for, so a card rendered again does not ask a second time.
    states[userId] = null;
    queued.add(userId);

    if (!flushScheduled) {
        flushScheduled = true;
        // After the current render, when every card of the page has queued its user.
        setTimeout(flush, 0);
    }
};

export default {
    name: 'MailPeopleCardAction',
    // The whole slot context is spread onto the component; what it does not use must not become
    // attributes of the button.
    inheritAttrs: false,
    props: {
        user: { type: Object, required: true },
        buttons: { type: Object, default: () => ({}) },
    },
    computed: {
        state() {
            return states[this.user.id] || null;
        },
        label() {
            return i18n.t('MailModule.base', 'Send message');
        },
    },
    watch: {
        'user.id': {
            immediate: true,
            handler(id) {
                if (id) {
                    request(id);
                }
            },
        },
    },
    methods: {
        compose() {
            modal.load(this.state.url);
        },
    },
};

// For the tests: forget what was loaded.
export const resetRecipientStates = () => {
    Object.keys(states).forEach((id) => delete states[id]);
    queued = new Set();
    flushScheduled = false;
    translations = null;
};
</script>
