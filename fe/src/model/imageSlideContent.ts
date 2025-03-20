import {SlideContent} from "./slideContent";

export class ImageSlideContent extends SlideContent {

    public imageUrl: string;

    constructor(imageUrl: string,) {
        super();
        this.imageUrl = imageUrl;
    }

    public toJSON(): object {
        return {
            type: "ImageSlideContent",
            imageUrl: this.imageUrl
        };
    }
}
