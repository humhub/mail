import { afterEach, beforeEach, describe, expect, it } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import MailPeopleCardAction, { resetRecipientStates } from '../../vue/components/MailPeopleCardAction.vue';

await import('@humhub-core/resources/js/humhub/humhub.url.js');
await import('@humhub-core/resources/js/humhub/humhub.vue.js');

let getCalls;
let preloads;
let states;
let wrappers;

const mountAction = (id, props = {}) => {
    const wrapper = mount(MailPeopleCardAction, {
        props: { user: { id, displayName: 'User ' + id }, ...props },
        // The slot spreads its whole context onto the action.
        attrs: { state: { isSelf: false }, followEnabled: true },
    });
    wrappers.push(wrapper);
    return wrapper;
};
const idsRequested = (url) => decodeURIComponent(new URL(url, 'http://localhost').searchParams.get('ids')).split(',').map(Number);
// The loader flushes after the current render (setTimeout 0).
const tick = () => new Promise((resolve) => setTimeout(resolve, 0)).then(flushPromises);

beforeEach(() => {
    resetRecipientStates();
    getCalls = [];
    preloads = [];
    wrappers = [];
    states = {
        2: { canMessage: true, url: '/mail/mail/create?userGuid=guid-2' },
        3: { canMessage: false, url: null },
    };

    globalThis.humhubStubs.client.get = (url) => {
        getCalls.push(url);
        const results = {};
        idsRequested(url).forEach((id) => {
            if (states[id]) {
                results[id] = states[id];
            }
        });
        return Promise.resolve({ results });
    };
    globalThis.humhubStubs.i18n.preload = (categories) => {
        preloads.push(categories);
        return Promise.resolve();
    };
});

afterEach(() => {
    wrappers.forEach((wrapper) => wrapper.unmount());
});

describe('MailPeopleCardAction', () => {
    it('asks for the users of all cards of a render in one request', async () => {
        const writable = mountAction(2);
        const refusing = mountAction(3);
        const invisible = mountAction(9);
        await tick();

        expect(getCalls).toHaveLength(1);
        expect(getCalls[0]).toContain('mail/recipient-states');
        expect(idsRequested(getCalls[0])).toEqual([2, 3, 9]);
        expect(preloads).toEqual([['MailModule.base']]);

        expect(writable.find('button').exists()).toBe(true);
        expect(refusing.find('button').exists()).toBe(false);
        expect(invisible.find('button').exists()).toBe(false);
    });

    it('renders an icon button labelled "Send message" on the card actions', async () => {
        const wrapper = mountAction(2);
        await tick();

        const button = wrapper.find('button');
        expect(button.attributes('aria-label')).toBe('Send message');
        expect(button.attributes('title')).toBe('Send message');
        expect(button.classes()).toEqual(expect.arrayContaining(['c-entity-card__action', 'btn', 'btn-light']));
        expect(button.find('i.ti.ti-mail').attributes('aria-hidden')).toBe('true');
        expect(button.text()).toBe('');
        // The rest of the slot context does not leak onto the button.
        expect(button.attributes('state')).toBeUndefined();
        expect(button.attributes('followenabled')).toBeUndefined();
    });

    it('takes the button classes of the directory when it names them', async () => {
        const wrapper = mountAction(2, { buttons: { mailClass: 'btn btn-accent' } });
        await tick();

        expect(wrapper.find('button').classes()).toEqual(expect.arrayContaining(['c-entity-card__action', 'btn', 'btn-accent']));
        expect(wrapper.find('button').classes()).not.toContain('btn-light');
    });

    it('opens the new conversation form in the global modal', async () => {
        const loaded = [];
        globalThis.humhubStubs.modal.global.load = (url) => {
            loaded.push(url);
            return Promise.resolve();
        };
        const wrapper = mountAction(2);
        await tick();

        await wrapper.find('button').trigger('click');

        expect(loaded).toEqual(['/mail/mail/create?userGuid=guid-2']);
    });

    it('asks for a user once per page load', async () => {
        mountAction(2);
        await tick();
        const again = mountAction(2);
        await tick();

        expect(getCalls).toHaveLength(1);
        expect(again.find('button').exists()).toBe(true);
    });

    it('renders nothing when the request fails', async () => {
        globalThis.humhubStubs.client.get = () => Promise.reject(new Error('offline'));
        const wrapper = mountAction(2);
        await tick();

        expect(wrapper.find('button').exists()).toBe(false);
    });
});
