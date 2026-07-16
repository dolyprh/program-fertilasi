export type MasterDataType =
    | 'doctors'
    | 'current-promils'
    | 'current-actions'
    | 'poa1-options';

export type MasterItem = {
    id: number;
    code: string;
    name: string;
    is_active: boolean;
    sort_order: number;
    created_at?: string;
    updated_at?: string;
};

export type MasterDataSummary = {
    key: MasterDataType;
    label: string;
    description: string;
    total: number;
    active: number;
};
