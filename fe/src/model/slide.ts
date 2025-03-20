import {SlideContent} from "./slideContent";
import {ImageSlideContent} from "./imageSlideContent";
import {RobotGameSlideContent} from "./robotGameSlideContent";

export class Slide {

    public id: number;
    public title: string;
    public content: SlideContent;

    constructor(id: number, title: string, content: SlideContent) {
        this.id = id;
        this.title = title;
        this.content = content;
    }

    public static fromArray(arr: object[]): Slide[] {
        return arr.map(obj => {
            let content: SlideContent;

            if (obj['content']) {
                switch (obj['content'].type) {
                    case "ImageSlideContent":
                        content = new ImageSlideContent(obj['content'].imageUrl);
                        break;
                    case "RobotGameSlideContent":
                        content = new RobotGameSlideContent();
                        break;
                    default:
                        console.error("Unknown slide content type: " + obj['content'].type);
                        content = null;
                }
            }
            return new Slide(obj['id'], obj['title'], content);
        });
    }
}
