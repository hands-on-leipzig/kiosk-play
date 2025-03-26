import {SlideContent} from "./slideContent";

export class ImageSlideContent extends SlideContent {

    public imageUrl: {};

    constructor(imageUrl: {},) {
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
