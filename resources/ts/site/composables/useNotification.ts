import { reactive } from "vue";

export type NotificationType =
    | "primary"
    | "info"
    | "success"
    | "warning"
    | "danger";

export interface INotification {
    id?: number;
    icon?: string;
    title?: string;
    label?: string;
    delay?: number;
    timeout: ReturnType<typeof setTimeout>;
    text: string;
    type: NotificationType;
}

export interface INotificationOptions {
    delay: number;
    defaultLabel: string;
    removeOnClick: boolean;
    align: "left" | "right";
}

export class NotificationsStore {
    constructor(options: INotificationOptions) {
        if (options) {
            this.settings = {
                ...options,
            };
        }
    }

    protected settings!: INotificationOptions;

    public get config(): INotificationOptions {
        return this.settings;
    }

    get list(): INotification[] {
        return this.state.list;
    }

    public state = reactive<{
        list: INotification[];
    }>({
        list: [],
    });

    public show(data: Partial<INotification>): INotification {
        let {
            icon,
            title,
            label = this.settings.defaultLabel,
            text = "",
            type = "primary",
            delay,
        } = data;

        let notificationID = Date.now();

        let newNotification: INotification = {
            id: notificationID,
            icon,
            title,
            label,
            text,
            type,
            timeout: setTimeout(
                () => this.remove(notificationID),
                delay || this.settings.delay
            ),
        };

        this.state.list.push(newNotification);

        return newNotification;
    }

    public remove(id: number): void {
        let findedItem = this.state.list.find((item) => item.id === id);
        if (findedItem) {
            clearTimeout(findedItem.timeout);
            this.state.list = this.state.list.filter(
                (item) => item.id !== findedItem.id
            );
        }
    }

    public removeAll() {
        this.state.list.forEach((item) => clearTimeout(item.timeout));
        this.state.list = [];
    }
}

export const useNotifications = new NotificationsStore({
    delay: 2000,
    defaultLabel: "",
    removeOnClick: true,
    align: "right",
});
