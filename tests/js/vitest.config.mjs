/**
 * Runs this module's Vue tests on the core's Vitest setup (runtime-only Vue, the `@humhub/vue`
 * shim, the humhub.module() environment) - the dependencies live in the core checkout:
 *
 *   cd $HUMHUB_PATH && npx vitest run --config <module>/tests/js/vitest.config.mjs
 */
import { fileURLToPath } from 'node:url';

const core = process.env.HUMHUB_PATH || process.cwd();
const here = fileURLToPath(new URL('.', import.meta.url));

const { mergeConfig } = await import(`${core}/node_modules/vitest/dist/config.js`);
const { default: coreConfig } = await import(`${core}/vitest.config.mjs`);

const config = mergeConfig(coreConfig, {
    root: core,
    resolve: {
        alias: {
            '@vue/test-utils': `${core}/node_modules/@vue/test-utils`,
            '@humhub-core': `${core}/protected/humhub`,
        },
    },
    server: { fs: { allow: [core, `${here}../..`] } },
});

// Replaced rather than merged: mergeConfig() would add the core's own tests to the run.
config.test.include = [`${here}**/*.test.js`];

export default config;
