/*!
 * AUTO-GENERATED FILE — do not edit.
 * Compiled from mail/vue/ via `grunt build-vue --module=mail`.
 * See docs/develop/ui-js-vuejs.md
 */
(function(vue$1, vue) {
  "use strict";
  const _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const MAX_IDS = 100;
  const states = vue.reactive({});
  let queued = /* @__PURE__ */ new Set();
  let flushScheduled = false;
  let translations = null;
  const flush = () => {
    flushScheduled = false;
    const ids = [...queued];
    queued = /* @__PURE__ */ new Set();
    translations ?? (translations = vue$1.i18n.preload(["MailModule.base"]).catch(() => void 0));
    for (let i = 0; i < ids.length; i += MAX_IDS) {
      const chunk = ids.slice(i, i + MAX_IDS);
      Promise.all([
        vue$1.client.get(vue$1.apiUrl("mail/recipient-states", { ids: chunk.join(",") })),
        translations
      ]).then(([response]) => {
        const results = response && response.results || {};
        chunk.forEach((id) => {
          states[id] = results[id] || null;
        });
      }).catch((error) => {
        vue$1.log.error("Could not load whether the users can be messaged", error);
      });
    }
  };
  const request = (userId) => {
    if (userId in states || queued.has(userId)) {
      return;
    }
    states[userId] = null;
    queued.add(userId);
    if (!flushScheduled) {
      flushScheduled = true;
      setTimeout(flush, 0);
    }
  };
  const _sfc_main = {
    name: "MailPeopleCardAction",
    // The whole slot context is spread onto the component; what it does not use must not become
    // attributes of the button.
    inheritAttrs: false,
    props: {
      user: { type: Object, required: true },
      buttons: { type: Object, default: () => ({}) }
    },
    computed: {
      state() {
        return states[this.user.id] || null;
      },
      label() {
        return vue$1.i18n.t("MailModule.base", "Send message");
      }
    },
    watch: {
      "user.id": {
        immediate: true,
        handler(id) {
          if (id) {
            request(id);
          }
        }
      }
    },
    methods: {
      compose() {
        vue$1.modal.load(this.state.url);
      }
    }
  };
  const _hoisted_1 = ["title", "aria-label"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return $options.state && $options.state.canMessage && $options.state.url ? (vue.openBlock(), vue.createElementBlock("button", {
      key: 0,
      type: "button",
      class: vue.normalizeClass(["c-entity-card__action", $props.buttons.mailClass || "btn btn-light"]),
      title: $options.label,
      "aria-label": $options.label,
      onClick: _cache[0] || (_cache[0] = (...args) => $options.compose && $options.compose(...args))
    }, [..._cache[1] || (_cache[1] = [
      vue.createElementVNode(
        "i",
        {
          class: "ti ti-mail",
          "aria-hidden": "true"
        },
        null,
        -1
        /* CACHED */
      )
    ])], 10, _hoisted_1)) : vue.createCommentVNode("v-if", true);
  }
  const MailPeopleCardAction = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  vue$1.register("MailPeopleCardAction", MailPeopleCardAction);
  vue$1.registerSlotComponent("user.card-actions", "MailPeopleCardAction", { id: "mail", sortOrder: 300 });
})(humhub.modules.vue, Vue);
//# sourceMappingURL=humhub.mail.vue.js.map
