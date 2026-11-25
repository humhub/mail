import { beforeEach, describe, expect, it } from 'vitest';

const { event } = globalThis.humhubStubs;

// The module chains `event.on(...).on(...)` as the core event bus allows.
const on = event.on;
event.on = function (...args) {
    on.apply(this, args);
    return this;
};

let visibility = 'visible';
Object.defineProperty(document, 'visibilityState', { configurable: true, get: () => visibility });

let instances = {};
globalThis.humhub.modules['ui.widget'] = { Widget: { instance: (selector) => instances[selector] || null } };
globalThis.humhub.modules['mail.notification'] = { setMailMessageCount: () => {} };

await import('../../resources/js/humhub.mail.conversation.js');

let root;
let seen;
let updatedEntries;
let resolveSeen;

const setVisibility = (state) => {
    visibility = state;
    document.dispatchEvent(new Event('visibilitychange'));
};
const receiveMessage = (messageId) => {
    event.trigger('humhub:modules:mail:live:NewUserMessage', [[{ data: { message_id: messageId } }]]);
};

beforeEach(() => {
    // Flushes anything a previous test left pending.
    setVisibility('visible');

    // The live handler only runs on the messenger page, which has the inbox.
    const inboxNode = document.createElement('div');
    inboxNode.id = 'inbox';
    document.body.replaceChildren(inboxNode);

    seen = [];
    updatedEntries = [];
    root = {
        options: { messageId: 5 },
        loadUpdate: () => {},
        markSeen: (id) => {
            seen.push(id);
            return new Promise((resolve) => {
                resolveSeen = resolve;
            });
        },
    };
    instances = {
        '#mail-conversation-root': root,
        '#inbox': { updateEntries: (ids) => updatedEntries.push(ids) },
    };
});

describe('a new message in the open conversation', () => {
    it('is marked seen right away while the page is visible', () => {
        receiveMessage(5);

        expect(seen).toEqual([5]);
    });

    it('is not marked seen while the page is hidden', () => {
        let loaded = false;
        root.loadUpdate = () => {
            loaded = true;
        };
        setVisibility('hidden');

        receiveMessage(5);

        expect(loaded).toBe(true);
        expect(seen).toEqual([]);
    });

    it('is marked seen once when the page becomes visible again', () => {
        setVisibility('hidden');
        receiveMessage(5);
        receiveMessage(5);
        setVisibility('hidden');
        expect(seen).toEqual([]);

        setVisibility('visible');
        setVisibility('visible');

        expect(seen).toEqual([5]);
    });

    it('refreshes its inbox entry after it was marked seen on becoming visible', async () => {
        setVisibility('hidden');
        receiveMessage(5);
        updatedEntries = [];

        setVisibility('visible');
        await Promise.resolve();
        expect(updatedEntries).toEqual([]);

        resolveSeen();
        await new Promise((resolve) => setTimeout(resolve, 0));
        expect(updatedEntries).toEqual([[5]]);
    });

    it('is not marked seen on becoming visible when another conversation is open by then', () => {
        setVisibility('hidden');
        receiveMessage(5);
        root.options.messageId = 7;

        setVisibility('visible');

        expect(seen).toEqual([]);
    });

    it('is not marked seen on becoming visible when the conversation is closed by then', () => {
        setVisibility('hidden');
        receiveMessage(5);
        delete instances['#mail-conversation-root'];

        setVisibility('visible');

        expect(seen).toEqual([]);
    });
});

describe('a new message in another conversation', () => {
    it('is not marked seen on becoming visible', () => {
        setVisibility('hidden');
        receiveMessage(9);

        setVisibility('visible');

        expect(seen).toEqual([]);
    });
});
