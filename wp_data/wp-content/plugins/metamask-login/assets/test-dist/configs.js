"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.unWalletConfigs = void 0;
exports.unWalletConfigs = {
    prod: {
        baseURL: "https://id.unwallet.world",
        wsAPIURL: "wss://in-ws-api.id.unwallet.world",
    },
    dev: {
        baseURL: "https://id.unwallet.dev",
        wsAPIURL: "wss://in-ws-api.id.unwallet.dev",
    },
    local: {
        baseURL: "http://localhost:4200",
        wsAPIURL: "wss://in-ws-api.id.test.unwallet.dev",
    },
};
