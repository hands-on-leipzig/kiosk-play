import { Score } from './score';
import { Category } from './category';

export class Team {
    id: number;
    name: string;
    scores: Score[];
    category: Category;
    rank: number;

    constructor(id: number, name: string, scores: Score[], category: Category, rank: number) {
        this.id = id;
        this.name = name;
        this.scores = scores;
        this.category = category;
        this.rank = rank;
    }
}
