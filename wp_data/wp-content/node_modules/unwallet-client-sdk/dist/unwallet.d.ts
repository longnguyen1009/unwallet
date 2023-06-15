import { Config, UnWalletConfig, DigestAndSignature, MetaTransaction } from "./types";
export declare class UnWallet {
    private config;
    private unWalletConfig;
    private ws;
    private connectionID;
    private resolve;
    private reject;
    constructor(config: Config, unWalletConfig: UnWalletConfig, ws: WebSocket);
    private initPromiseArgs;
    static init(config: Config): Promise<UnWallet>;
    authorize(args: {
        responseMode?: string;
        redirectURL: string;
        nonce?: string;
        isVirtual?: boolean;
    }): void;
    sign(args: {
        message: string;
    }): Promise<DigestAndSignature>;
    signTransaction(args: {
        to: string;
        value?: string;
        data?: string;
    }): Promise<MetaTransaction>;
    signTokenTransfer(args: {
        id: number;
        to: string;
        amount: number;
    }): Promise<MetaTransaction>;
    createPresentation(args: {
        credential: string;
        challenge: string;
    }): Promise<string>;
    private getConnectionID;
    private sendWSMessage;
    private handleWSMessage;
    private openWindow;
}
//# sourceMappingURL=unwallet.d.ts.map