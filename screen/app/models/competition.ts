import { Category } from './category';

export class Competition {
    id: number;
    internalId: number;
    name: string;
    categories: Category[];

    constructor(id: number, internalId: number, name: string, categories: Category[]) {
        this.id = id;
        this.internalId = internalId;
        this.name = name;
        this.categories = categories;
    }
}
