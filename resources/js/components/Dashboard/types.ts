export type Transaction = {
    id: number;
    title: string;
    amount: number;
    type: 'income' | 'expense';
    category?: { name: string } | null;
    created_at: string;
};

export type Category = {
    id: number;
    name: string;
    type: 'income' | 'expense';
};

export type FamilyMember = {
    id: number;
    name: string;
    email: string;
};

export type Family = {
    name: string;
    invite_code: string;
    total_balance: number;
};
