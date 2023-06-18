"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.UnWallet = void 0;
const ethers_1 = require("ethers");
const configs_1 = require("./configs");
class UnWallet {
    constructor(config, unWalletConfig, ws) {
        this.resolve = null;
        this.reject = null;
        this.config = config;
        this.unWalletConfig = unWalletConfig;
        this.ws = ws;
        this.connectionID = "";
        this.initPromiseArgs();
    }
    initPromiseArgs() {
        this.resolve = (result) => { };
        this.reject = (reason) => { };
    }
    static init(config) {
        return new Promise((resolve, reject) => {
            if (config.env === undefined) {
                config.env = "prod";
            }
            if (!(config.env in configs_1.unWalletConfigs)) {
                throw Error("invalid env");
            }
            const unWalletConfig = configs_1.unWalletConfigs[config.env];
            const ws = new WebSocket(unWalletConfig.wsAPIURL);
            ws.onerror = (event) => {
                reject("websocket connection failed");
            };
            ws.onopen = (event) => {
                unWallet.getConnectionID();
            };
            ws.onmessage = (event) => {
                const msg = JSON.parse(event.data);
                if (msg.type === "connectionID") {
                    unWallet.connectionID = msg.value;
                    resolve(unWallet);
                    return;
                }
                unWallet.handleWSMessage(msg);
            };
            // should be run after ws setup
            const unWallet = new UnWallet(config, unWalletConfig, ws);
        });
    }
    authorize(args) {
        if (!args.responseMode) {
            args.responseMode = "fragment";
        }
        let url;
        if (args.isVirtual === false) {
            url = new URL(`${this.unWalletConfig.baseURL}/authorize`);
        }
        else {
            url = new URL(`${this.unWalletConfig.baseURL}/vauthorize`);
        }
        url.searchParams.set("response_type", "id_token");
        url.searchParams.set("response_mode", args.responseMode);
        url.searchParams.set("client_id", this.config.clientID);
        url.searchParams.set("scope", "openid email");
        url.searchParams.set("redirect_uri", args.redirectURL);
        if (args.nonce !== undefined) {
            url.searchParams.set("nonce", args.nonce);
        }
        location.assign(url.toString());
    }
    sign(args) {
        return new Promise((resolve, reject) => {
            this.resolve = (sig) => {
                resolve({
                    digest: ethers_1.ethers.utils.sha256(ethers_1.ethers.utils.toUtf8Bytes(args.message)),
                    signature: sig,
                });
            };
            this.reject = reject;
            const url = new URL(`${this.unWalletConfig.baseURL}/x/sign`);
            url.searchParams.set("connectionID", this.connectionID);
            url.searchParams.set("clientID", this.config.clientID);
            url.searchParams.set("message", args.message);
            this.openWindow(url);
        });
    }
    signTransaction(args) {
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
            const url = new URL(`${this.unWalletConfig.baseURL}/x/signTransaction`);
            url.searchParams.set("connectionID", this.connectionID);
            url.searchParams.set("clientID", this.config.clientID);
            url.searchParams.set("transaction", JSON.stringify(args));
            this.openWindow(url);
        });
    }
    signTokenTransfer(args) {
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
            const url = new URL(`${this.unWalletConfig.baseURL}/x/signTokenTransfer`);
            url.searchParams.set("connectionID", this.connectionID);
            url.searchParams.set("clientID", this.config.clientID);
            url.searchParams.set("id", args.id.toString());
            url.searchParams.set("to", args.to);
            url.searchParams.set("amount", args.amount.toString());
            this.openWindow(url);
        });
    }
    createPresentation(args) {
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
            const url = new URL(`${this.unWalletConfig.baseURL}/x/createPresentation`);
            url.searchParams.set("connectionID", this.connectionID);
            url.searchParams.set("clientID", this.config.clientID);
            url.searchParams.set("credential", args.credential);
            url.searchParams.set("challenge", args.challenge);
            this.openWindow(url);
        });
    }
    getConnectionID() {
        this.sendWSMessage({
            action: "getConnectionID",
        });
    }
    sendWSMessage(msg) {
        this.ws.send(JSON.stringify(msg));
    }
    handleWSMessage(msg) {
        switch (msg.type) {
            case "signature":
                this.resolve(msg.value);
                break;
            case "metaTransaction":
                this.resolve(msg.value);
                break;
            case "presentation":
                this.resolve(msg.value);
                break;
            case "error":
                switch (msg.value) {
                    case "rejected":
                        this.reject("canceled");
                        break;
                    default:
                        throw new Error(msg.value);
                }
                break;
            default:
                throw new Error(`unknown message type: ${msg.type}`);
        }
        this.initPromiseArgs();
    }
    openWindow(url) {
        const width = screen.width / 2;
        const height = screen.height;
        const left = screen.width / 4;
        const top = 0;
        window.open(url.toString(), "_blank", `width=${width},height=${height},left=${left},top=${top}`);
    }
}
exports.UnWallet = UnWallet;
