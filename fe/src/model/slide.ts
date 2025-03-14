import {SlideContent} from "./slideContent";

export class Slide {

    public id: number;
    public title: string;
    public content: SlideContent;

    constructor(id: number, title: string, content: SlideContent) {
        this.id = id;
        this.title = title;
        this.content = content;
    }
}
