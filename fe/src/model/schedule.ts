

export class Schedule {

    public id: number;
    public name: string;
    public event: number;
    public created: number;
    public lastUpdate: number;
    public published: boolean;

    constructor(id: number) {
        this.id = id;
    }


}
