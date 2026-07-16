export type ClassificationStatus = 'classified' | 'no_rule';

export type MasterOption = {
    id: number;
    code: string;
    name: string;
};

export type Doctor = MasterOption;
export type CurrentPromil = MasterOption;
export type CurrentAction = MasterOption;
export type Poa1Option = MasterOption;

export type Classification = {
    id: number;
    user_id: number;
    doctor_id: number;
    current_promil_id: number;
    current_action_id: number;
    poa1_option_id: number;
    case_code: string;
    patient_name: string;
    visit_date: string;
    date_of_birth: string;
    age: number;
    prediction_result: string | null;
    rule_code: string | null;
    decision_path: string[] | null;
    confidence: string | number | null;
    support_count: number | null;
    support_percentage: string | number | null;
    status: ClassificationStatus;
    created_at: string;
    updated_at: string;
    doctor?: Doctor;
    current_promil?: CurrentPromil;
    current_action?: CurrentAction;
    poa1_option?: Poa1Option;
};

export type DecisionRule = {
    code: string;
    conditions: string[];
    prediction: string;
    confidence: number;
    support_count: number;
    support_percentage: number;
};

export type ModelInformation = {
    algorithm: string;
    criterion: string;
    training_count: number;
    testing_count: number;
    root_node: string;
    maximal_depth: number;
    minimal_gain: number;
    minimal_leaf_size: number;
    pruning: boolean;
    prepruning: boolean;
    accuracy: number;
    macro_precision: number;
    macro_recall: number;
    macro_f1: number;
};

export type TrainingAgeRange = {
    min: number;
    max: number;
};

export type ClassificationFilters = {
    case_code: string;
    current_promil_id: string;
    prediction_result: string;
    status: string;
};

export type ResultDistributionItem = {
    label: string;
    total: number;
};

export type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
};
