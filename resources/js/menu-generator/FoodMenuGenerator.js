import { PDFDocument, rgb, degrees } from "pdf-lib";
export class FoodIntolerance {
}
export class FoodAllergy {
}
export class FoodIngredient {
}
export class FoodMenuItem {
}
export class MenuCategory {
}
export class FoodMenu {
}
export class FoodMenuQuery {
}
export class PdfImage {
    constructor(obj) {
        this.rotation = 0;
        this.opacity = 1;
        this.opacity = 1;
        this.rotation = 0;
        Object.assign(this, obj);
    }
}
export class PageColumn {
    constructor(page, xStartPos, yStartPos, totalContentWidth, totalContentHeight, padding = 0) {
        this.page = page;
        this.columnCursor = xStartPos;
        this.heightCursor = yStartPos;
        this.totalContentWidth = totalContentWidth;
        this.totalContentHeight = totalContentHeight;
        this.padding = padding;
        this.xStartPos = xStartPos;
        this.yStartPos = yStartPos;
        this.xEndPos = xStartPos + totalContentWidth;
        this.yEndPos = yStartPos - totalContentHeight;
    }
    reset() {
        this.heightCursor = this.yStartPos;
    }
    switchColumn(newXStartPos) {
        this.columnCursor = newXStartPos;
        this.reset();
    }
}
export class FoodMenuGenerator {
    constructor(menuData) {
        this.allergiesImageMap = new Map();
        this.cachedImages = new Map();
        this.menuData = menuData;
        this.initAllergiesImageMap();
    }
    initAllergiesImageMap() {
        this.allergiesImageMap.set('gluten', {
            imagePath: 'assets/images/allergies/gluten.png',
            descriptionIt: 'Cereali contenenti glutine e prodotti derivati',
            descriptionDe: 'Glutenhaltiges Getreide',
            descriptionEn: 'Cereals containing gluten and derived products',
        });
        this.allergiesImageMap.set('crustaceans', {
            imagePath: 'assets/images/allergies/crustaceans.png',
            descriptionIt: 'Crostacei e prodotti derivati e loro derivati',
            descriptionDe: 'Krebstiere und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Crustaceans and derived products',
        });
        this.allergiesImageMap.set('eggs', {
            imagePath: 'assets/images/allergies/eggs.png',
            descriptionIt: 'Uova e prodotti derivati',
            descriptionDe: 'Eier und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Eggs and derived products',
        });
        this.allergiesImageMap.set('fish', {
            imagePath: 'assets/images/allergies/fish.png',
            descriptionIt: 'Pesce e prodotti derivati',
            descriptionDe: 'Fisch und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Fish and derived products',
        });
        this.allergiesImageMap.set('peanuts', {
            imagePath: 'assets/images/allergies/peanuts.png',
            descriptionIt: 'Arachidi e prodotti derivati',
            descriptionDe: 'Erdnüsse und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Peanuts and derived products',
        });
        this.allergiesImageMap.set('soybeans', {
            imagePath: 'assets/images/allergies/soybeans.png',
            descriptionIt: 'Soia e prodotti derivati',
            descriptionDe: 'Sojabohnen und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Soybeans and derived products',
        });
        this.allergiesImageMap.set('milk', {
            imagePath: 'assets/images/allergies/milk.png',
            descriptionIt: 'Latte e prodotti derivati',
            descriptionDe: 'Milch und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Milk and derived products',
        });
        this.allergiesImageMap.set('nuts', {
            imagePath: 'assets/images/allergies/nuts.png',
            descriptionIt: 'Frutta a guscio',
            descriptionDe: 'Schalenfrüchte',
            descriptionEn: 'Nuts',
        });
        this.allergiesImageMap.set('celery', {
            imagePath: 'assets/images/allergies/celery.png',
            descriptionIt: 'Sedano e prodotti derivati',
            descriptionDe: 'Sellerie und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Celery and derived products',
        });
        this.allergiesImageMap.set('mustard', {
            imagePath: 'assets/images/allergies/mustard.png',
            descriptionIt: 'Senape e prodotti a base di senape',
            descriptionDe: 'Senf und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Mustard and derived products',
        });
        this.allergiesImageMap.set('sesame', {
            imagePath: 'assets/images/allergies/sesame.png',
            descriptionIt: 'Semi di sesamo e prodotti a base di semi di sesamo',
            descriptionDe: 'Sesamsamen und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Sesame seeds and derived products',
        });
        this.allergiesImageMap.set('sulphurDioxide', {
            imagePath: 'assets/images/allergies/sulphur-dioxide.png',
            descriptionIt: 'Anidride solforosa e solfiti',
            descriptionDe: 'Schwefeldioxid und Sulfite',
            descriptionEn: 'Sulphur dioxide and sulphites',
        });
        this.allergiesImageMap.set('lupin', {
            imagePath: 'assets/images/allergies/lupin.png',
            descriptionIt: 'Lupini e prodotti a base di lupini',
            descriptionDe: 'Lupinen und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Lupin and derived products',
        });
        this.allergiesImageMap.set('molluscs', {
            imagePath: 'assets/images/allergies/molluscs.png',
            descriptionIt: 'Molluschi e prodotti derivati',
            descriptionDe: 'Weichtiere und daraus gewonnene Erzeugnisse',
            descriptionEn: 'Molluscs and derived products',
        });
    }
    async generateMenu() {
        this.pdf = await PDFDocument.create();
        let page = this.addNewPage();
        const { width, height } = page.getSize();
        const contentWidth = width - 2 * FoodMenuGenerator.SIDE_MARGIN;
        let heightCursor = height - FoodMenuGenerator.TOP_PAGE_MARGIN;
        let oldPage = page;
        let currentCategoryIndex = 0;
        for (const category of this.menuData) {
            const isLastCategory = this.menuData.length <= currentCategoryIndex + 1;
            const expectedHeight = this.getExpectedHeightOfCategory(category.foodMenuItems.length)
                + FoodMenuGenerator.CATEGORY_FONT_SIZE
                + (isLastCategory ? 0 : FoodMenuGenerator.CATEGORY_SPACING);
            const diff = heightCursor - expectedHeight - (isLastCategory ? -20 : FoodMenuGenerator.CATEGORY_SPACING);
            if (diff < FoodMenuGenerator.BOTTOM_PAGE_MARGIN &&
                heightCursor != this.getStartingHeightCursor(page) // if we are not at the start of the page
            ) {
                page = this.addNewPage();
                heightCursor = this.getStartingHeightCursor(page);
                oldPage = page;
            }
            this.drawCategoryTitle(page, category.name, contentWidth, heightCursor);
            heightCursor -= FoodMenuGenerator.LINE_SPACE + 10;
            ({ page, heightCursor } = await this.drawCategoryItems(page, category.foodMenuItems, contentWidth, heightCursor, isLastCategory));
            heightCursor -= FoodMenuGenerator.CATEGORY_SPACING;
            if (oldPage != page &&
                !isLastCategory && // if we have more categories
                heightCursor < this.getStartingHeightCursor(page) &&
                heightCursor > FoodMenuGenerator.BOTTOM_PAGE_MARGIN + FoodMenuGenerator.LINE_SPACE) {
                // the page we provided and the page we got back are different
                // means the current category had too much items that couldn't covered on the same page
                // thus we need a new page to continue with next category because its not beautiful to start new category
                // on continued category page unless its the last category started on the same page
                const nextCategoryExpectedHeight = this.getExpectedHeightOfCategory(this.menuData[currentCategoryIndex + 1].foodMenuItems.length)
                    + FoodMenuGenerator.CATEGORY_FONT_SIZE
                    + (isLastCategory ? 0 : FoodMenuGenerator.CATEGORY_SPACING);
                if (nextCategoryExpectedHeight > heightCursor) {
                    // if next category can't fit in the same page, then we need to add a new page
                    page = this.addNewPage();
                    heightCursor = this.getStartingHeightCursor(page);
                }
                // page.drawText(category.name!, {
                //   x: FoodMenuGenerator.SIDE_MARGIN,
                //   y: heightCursor - 50,
                //   size: FoodMenuGenerator.CATEGORY_FONT_SIZE,
                // })
            }
            oldPage = page;
            currentCategoryIndex++;
            // page.drawLine({
            //   start: {
            //     x: 0, y: heightCursor
            //   },
            //   end: {
            //     x: 500, y: heightCursor
            //   }
            // })
        }
    }
    getStartingHeightCursor(page) {
        return page.getHeight() - FoodMenuGenerator.TOP_PAGE_MARGIN;
    }
    getExpectedHeightOfCategory(totalItems) {
        const itemNameSize = (totalItems * FoodMenuGenerator.FONT_SIZE) +
            (totalItems * FoodMenuGenerator.LINE_SPACE) +
            FoodMenuGenerator.CATEGORY_FONT_SIZE;
        // add also descriptions size too
        const totalDescriptions = 3; // 3 languages
        // TODO: improve here. Get descriptions and calculate height of each description, that would be more realistic height
        const descriptionSize = (totalItems * totalDescriptions * FoodMenuGenerator.DESCRIPTION_TEXT_FONT_SIZE);
        // + (totalItems * totalDescriptions * FoodMenuGenerator.LINE_SPACE);
        return itemNameSize + descriptionSize;
    }
    async savePdf() {
        const pdfBytes = await this.pdf.save();
        this.openPdfFileInNewTab(pdfBytes);
    }
    drawCategoryTitle(page, name, contentWidth, heightCursor) {
        const xPos = this.getCenterTextStartXPosition(contentWidth, name);
        page.drawText(name, {
            x: xPos + FoodMenuGenerator.SIDE_MARGIN,
            y: heightCursor,
            size: FoodMenuGenerator.CATEGORY_FONT_SIZE,
        });
    }
    async drawCategoryItems(page, items, contentWidth, heightCursor, isLast = false) {
        var _a;
        // page.drawLine({
        //   start: {x: 1, y: heightCursor},
        //   end: {x: page.getWidth() - 1, y: heightCursor},
        //   color: rgb(0.95, .14, .44),
        // })
        for (const item of items) {
            const expectedSpacePerItem = FoodMenuGenerator.FONT_SIZE + FoodMenuGenerator.LINE_SPACE + 3 * FoodMenuGenerator.DESCRIPTION_TEXT_FONT_SIZE + 6;
            if ((heightCursor - expectedSpacePerItem) < FoodMenuGenerator.BOTTOM_PAGE_MARGIN + FoodMenuGenerator.LINE_SPACE) {
                page = this.addNewPage();
                heightCursor = page.getHeight() - FoodMenuGenerator.TOP_PAGE_MARGIN;
            }
            // add images for allergies
            const keys = (_a = item.foodAllergies) === null || _a === void 0 ? void 0 : _a.map((allergy) => allergy.key);
            page.drawText(item.name, {
                x: FoodMenuGenerator.SIDE_MARGIN + FoodMenuGenerator.ITEM_NAME_OFFSET,
                y: heightCursor,
                size: FoodMenuGenerator.FONT_SIZE,
            });
            if (keys && keys.length > 0) {
                // add images front of the item name
                const xPos = this.getTextWidth(item.name.trim(), FoodMenuGenerator.FONT_SIZE)
                    + FoodMenuGenerator.SIDE_MARGIN
                    + FoodMenuGenerator.ITEM_NAME_OFFSET + 5;
                await this.addAllergiesImages(page, keys, xPos, heightCursor - 3);
            }
            this.drawPrice(page, item.price, heightCursor);
            heightCursor -= FoodMenuGenerator.LINE_SPACE;
            let hasDescriptions = false;
            if (item.descriptionDe && item.descriptionDe.length > 0) {
                heightCursor = this.drawItemDescription(page, item.descriptionDe, heightCursor);
                hasDescriptions = true;
            }
            if (item.descriptionIt && item.descriptionIt.length > 0) {
                heightCursor = this.drawItemDescription(page, item.descriptionIt, heightCursor);
                hasDescriptions = true;
            }
            if (item.descriptionEn && item.descriptionEn.length > 0) {
                heightCursor = this.drawItemDescription(page, item.descriptionEn, heightCursor);
                hasDescriptions = true;
            }
            if (hasDescriptions) {
                heightCursor -= FoodMenuGenerator.LINE_SPACE;
            }
        }
        heightCursor -= FoodMenuGenerator.LINE_SPACE - (isLast ? 20 : 5);
        // draw line at this point horizontal to see where the line or cursor at the moment
        // page.drawLine({
        //   start: {x: 1, y: heightCursor},
        //   end: {x: page.getWidth() - 1, y: heightCursor},
        // })
        return { page, heightCursor };
    }
    getTextWidth(text, fontSize) {
        // use dom element to calculate text width
        const span = document.createElement('span');
        span.style.fontSize = `${fontSize}px`;
        span.style.visibility = 'hidden';
        span.style.position = 'absolute';
        span.style.top = '-100%';
        span.style.left = '-100%';
        span.style.whiteSpace = 'nowrap';
        span.style.fontFamily = 'Arial';
        span.innerText = text;
        document.body.appendChild(span);
        const width = span.offsetWidth;
        document.body.removeChild(span);
        return width;
        // return text.replaceAll(' ', '').length * fontSize; // Approximate character width
    }
    async addAllergiesImages(page, allergies, xPos, yPos) {
        for (const allergy of allergies) {
            if (!this.allergiesImageMap.has(allergy)) continue;
            const { imagePath } = this.allergiesImageMap.get(allergy);
            const imageBytes = await this.loadImage(imagePath);
            const { pngImageByte, width, height } = await this.convertImageToPng(imageBytes);
            const image = await this.pdf.embedPng(pngImageByte);
            const imageSize = 18;
            page.drawImage(image, {
                x: xPos,
                y: yPos,
                width: imageSize,
                height: imageSize,
            });
            xPos += imageSize + 8;
        }
    }
    drawItemDescription(page, description, heightCursor) {
        const wrappedText = this.wrapText(description, FoodMenuGenerator.MAX_TEXT_WIDTH, FoodMenuGenerator.DESCRIPTION_TEXT_FONT_SIZE);
        wrappedText.forEach(line => {
            page.drawText(line, {
                x: FoodMenuGenerator.SIDE_MARGIN + FoodMenuGenerator.ITEM_NAME_OFFSET,
                y: heightCursor,
                size: FoodMenuGenerator.DESCRIPTION_TEXT_FONT_SIZE,
            });
            heightCursor -= FoodMenuGenerator.LINE_SPACE;
        });
        return heightCursor;
    }
    wrapText(text, maxWidth, fontSize) {
        const words = text.split(' ');
        const lines = [];
        let currentLine = '';
        words.forEach(word => {
            const testLine = currentLine ? `${currentLine} ${word}` : word;
            const testLineWidth = testLine.length * (fontSize * 0.6); // Approximate character width
            if (testLineWidth > maxWidth) {
                if (currentLine) {
                    lines.push(currentLine);
                    currentLine = word;
                }
                else {
                    lines.push(testLine); // Word itself is longer than maxWidth, just push
                    currentLine = '';
                }
            }
            else {
                currentLine = testLine;
            }
        });
        if (currentLine) {
            lines.push(currentLine);
        }
        return lines;
    }
    drawPrice(page, price, yPos) {
        let formattedPrice = price.includes("/") ? price : price;
        if (formattedPrice && !formattedPrice.includes('/') && !formattedPrice.includes('--')) {
            formattedPrice = formattedPrice.replace('.', ',');
            formattedPrice = formattedPrice.replace('€', '').trim();
            formattedPrice = `${formattedPrice}€`;
        }
        const priceX = page.getWidth() - FoodMenuGenerator.SIDE_MARGIN - FoodMenuGenerator.PRICE_OFFSET;
        page.drawText(formattedPrice, {
            x: priceX,
            y: yPos + (FoodMenuGenerator.LINE_SPACE / 2), // Align prices with text
            size: FoodMenuGenerator.PRICE_FONT_SIZE,
        });
    }
    addNewPage() {
        const page = this.pdf.addPage();
        this.addHeaderToPdfPage(page, 'Alpin Curry');
        page.drawLine({
            start: { x: 1, y: FoodMenuGenerator.BOTTOM_PAGE_MARGIN },
            end: { x: page.getWidth() - 1, y: FoodMenuGenerator.BOTTOM_PAGE_MARGIN },
        });
        return page;
    }
    async addAllergiesImagesInFooter() {
        const pages = this.pdf.getPages();
        for (let i = 0; i < pages.length; i++) {
            const page = pages[i];
            // draw all images in bottom of allergies
            for (let i = 0; i < this.allergiesImageMap.size; i++) {
                const { imagePath } = this.allergiesImageMap.get(Array.from(this.allergiesImageMap.keys())[i]);
                const imageBytes = await this.loadImage(imagePath);
                const { pngImageByte, width, height } = await this.convertImageToPng(imageBytes);
                const image = await this.pdf.embedPng(pngImageByte);
                const imageSize = 18;
                page.drawImage(image, {
                    x: FoodMenuGenerator.SIDE_MARGIN + (i * (imageSize + 8)),
                    y: FoodMenuGenerator.BOTTOM_PAGE_MARGIN - imageSize - 10,
                    width: imageSize,
                    height: imageSize,
                });
            }
        }
    }
    async addAllergiesImagesTableWithDescriptionFooter(lang = 'rotational') {
        const pages = this.pdf.getPages();
        const legendStartX = 10;
        const legendStartY = FoodMenuGenerator.BOTTOM_PAGE_MARGIN - 20; // Initial Y position for the legend
        const imageSize = 18;
        const descriptionWidth = 130; // Max width for description text before wrapping
        const legendFontSize = 8;
        if (lang === 'rotational') {
            const languages = ['it', 'de', 'en'];
            for (let i = 0; i < pages.length; i++) {
                const page = pages[i];
                const lang = languages[i % languages.length];
                await this.addAllergiesLegend(page, legendStartX, legendStartY, lang, imageSize, descriptionWidth, legendFontSize, legendStartX);
            }
        }
        else {
            for (const page of pages) {
                await this.addAllergiesLegend(page, legendStartX, legendStartY, lang, imageSize, descriptionWidth, legendFontSize, legendStartX);
            }
        }
    }
    async addAllergiesLegend(page, currentX, currentY, descriptionLang, imageSize = 18, descriptionWidth = 130, legendFontSize = 8, legendStartX = 10) {
        for (const allergyKey of this.allergiesImageMap.keys()) {
            const { imagePath, descriptionIt, descriptionDe, descriptionEn } = this.allergiesImageMap.get(allergyKey);
            let pngImageByte, width, height;
            if (this.cachedImages.has(imagePath)) {
                ({ pngImageByte, width, height } = this.cachedImages.get(imagePath));
            }
            else {
                const imageBytes = await this.loadImage(imagePath);
                ({ pngImageByte, width, height } = await this.convertImageToPng(imageBytes));
                this.cachedImages.set(imagePath, { pngImageByte, width, height });
            }
            const image = await this.pdf.embedPng(pngImageByte);
            // Draw the allergy image
            page.drawImage(image, {
                x: currentX,
                y: currentY,
                width: imageSize,
                height: imageSize,
            });
            // Select the description based on the specified language
            let description = '';
            switch (descriptionLang) {
                case 'it':
                    description = descriptionIt;
                    break;
                case 'de':
                    description = descriptionDe;
                    break;
                case 'en':
                    description = descriptionEn;
                    break;
            }
            // Wrap the description text
            const wrappedText = this.wrapText(description, descriptionWidth, legendFontSize);
            // Draw the wrapped text
            let textY = currentY + 7; // Position text below the image
            for (const line of wrappedText) {
                page.drawText(line, { x: currentX + imageSize + 10, y: textY, size: legendFontSize });
                textY -= 8; // Move down for next line of text
            }
            // Move to the next column position
            currentX += imageSize + descriptionWidth;
            // Check if the next item would overflow the page width, and move to the next row if necessary
            if (currentX + descriptionWidth > page.getWidth()) {
                currentX = legendStartX;
                currentY = textY - 20; // Move down to start a new row
            }
        }
    }
    addHeaderToPdfPage(page, title) {
        const { width, height } = page.getSize();
        const headerHeight = 40;
        const headerFontSize = 28;
        const subHeaderFontSize = 20;
        page.drawText(title, {
            x: this.getCenterTextStartXPosition(width, title, headerFontSize),
            y: height - headerHeight,
            size: headerFontSize,
            color: rgb(...this.toDecimalRGB(248, 178, 51)),
        });
    }
    getCenterTextStartXPosition(width, text, fontSize = FoodMenuGenerator.CATEGORY_FONT_SIZE) {
        const averageCharWidthFactor = 0.45; // Better approximate character width
        const textWidth = text.length * fontSize * averageCharWidthFactor;
        return (width - textWidth) / 2;
    }
    toDecimalRGB(r, g, b, decimals = 2) {
        const factor = Math.pow(10, decimals);
        return [
            Math.round((r / 255) * factor) / factor,
            Math.round((g / 255) * factor) / factor,
            Math.round((b / 255) * factor) / factor,
        ];
    }
    openPdfFileInNewTab(pdfBytes) {
        const blob = new Blob([pdfBytes], { type: 'application/pdf' });
        const url = URL.createObjectURL(blob);
        window.open(url, '_blank');
    }
    async addImagesToPdf(imageSpecs, allPages = true, pageIndexes) {
        const pages = this.pdf.getPages();
        const validIndexes = pageIndexes === null || pageIndexes === void 0 ? void 0 : pageIndexes.filter((index) => index >= 0 && index < pages.length);
        for (const spec of imageSpecs) {
            const imageBytes = await this.loadImage(spec.image);
            const { pngImageByte, width, height } = await this.convertImageToPng(imageBytes);
            const image = await this.pdf.embedPng(pngImageByte);
            const pagesToDrawOn = allPages ? pages : (validIndexes === null || validIndexes === void 0 ? void 0 : validIndexes.map((i) => pages[i])) || [];
            pagesToDrawOn.forEach((page) => {
                page.drawImage(image, {
                    x: spec.xPos,
                    y: spec.yPos,
                    width: spec.width || width,
                    height: spec.height || height,
                    rotate: degrees(spec.rotation),
                    opacity: spec.opacity,
                });
            });
        }
    }
    async addImagesRandomToPdf(imageSpecs, allPages = false, pageIndexes) {
        const pages = this.pdf.getPages();
        const validIndexes = pageIndexes === null || pageIndexes === void 0 ? void 0 : pageIndexes.filter((index) => index >= 0 && index < pages.length);
        for (const spec of imageSpecs) {
            const imageBytes = await this.loadImage(spec.image);
            const { pngImageByte, width, height } = await this.convertImageToPng(imageBytes);
            const image = await this.pdf.embedPng(pngImageByte);
            const pagesToDrawOn = allPages ? pages : (validIndexes === null || validIndexes === void 0 ? void 0 : validIndexes.map((i) => pages[i])) || [];
            const imageWidth = spec.width || width;
            const imageHeight = spec.height || height;
            pagesToDrawOn.forEach((page) => {
                const randomX = Math.random() * (page.getWidth() - imageWidth);
                const randomY = Math.random() * (page.getHeight() - imageHeight);
                page.drawImage(image, {
                    x: randomX,
                    y: randomY,
                    width: imageWidth,
                    height: imageHeight,
                    rotate: degrees(spec.rotation),
                    opacity: spec.opacity,
                });
            });
        }
    }
    async convertImageToPng(imageBytes) {
        // Create a Blob from the image bytes
        const blob = new Blob([imageBytes]);
        // Create an image element
        const img = new Image();
        // Set the image source and crossOrigin attribute to avoid tainted canvas
        img.src = URL.createObjectURL(blob);
        img.crossOrigin = 'anonymous';
        // Wait for the image to load
        await new Promise((resolve, reject) => {
            img.onload = () => resolve();
            img.onerror = () => reject(new Error('Failed to load image'));
        });
        // Create a canvas and draw the image onto it
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = img.width;
        canvas.height = img.height;
        ctx === null || ctx === void 0 ? void 0 : ctx.drawImage(img, 0, 0);
        // Convert the canvas to a PNG blob
        const pngBlob = await new Promise((resolve, reject) => {
            canvas.toBlob((blob) => {
                if (blob) {
                    resolve(blob);
                }
                else {
                    reject(new Error('Canvas toBlob conversion failed'));
                }
            }, 'image/png'); // Convert to PNG
        });
        // Read the blob as an ArrayBuffer and convert to Uint8Array
        const pngImageByte = new Uint8Array(await pngBlob.arrayBuffer());
        // Revoke the object URL to release memory
        URL.revokeObjectURL(img.src);
        return {
            pngImageByte,
            width: canvas.width,
            height: canvas.height,
        };
    }
    async loadImage(imagePath) {
        if (this.isUrl(imagePath)) {
            const response = await fetch(imagePath);
            const arrayBuffer = await response.arrayBuffer();
            return new Uint8Array(arrayBuffer);
        }
        else {
            // Load image from local file path
            const origin = window.location.origin;
            const url = `${origin}/${imagePath}`;
            const response = await fetch(url);
            return new Uint8Array(await response.arrayBuffer());
        }
    }
    isUrl(url) {
        const is = url.startsWith('http://') || url.startsWith('https://') || url.startsWith('www.');
        if (!is) {
            try {
                const urlParts = new URL(url);
                return urlParts.origin != null && urlParts.origin.length > 5;
            }
            catch (e) {
                return false;
            }
        }
        return is;
    }
}
// Class-level constants for layout and styling
FoodMenuGenerator.TOP_PAGE_MARGIN = 65;
FoodMenuGenerator.BOTTOM_PAGE_MARGIN = 100;
FoodMenuGenerator.SIDE_MARGIN = 20;
FoodMenuGenerator.CATEGORY_FONT_SIZE = 18;
FoodMenuGenerator.PRICE_FONT_SIZE = 10;
FoodMenuGenerator.FONT_SIZE = 16;
FoodMenuGenerator.DESCRIPTION_TEXT_FONT_SIZE = 10;
FoodMenuGenerator.LINE_SPACE = 12;
FoodMenuGenerator.CATEGORY_SPACING = 8; // Space after each category
FoodMenuGenerator.ITEM_NAME_OFFSET = 5; // Offset for item names from the start
FoodMenuGenerator.PRICE_OFFSET = 50; // Offset for prices
FoodMenuGenerator.MAX_TEXT_WIDTH = 680; // Max width for text before wrapping
