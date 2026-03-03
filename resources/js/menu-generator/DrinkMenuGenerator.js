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
export class DrinkMenuGenerator {
    constructor(drinksData, columnCount = 1, drawLeftToRight = true) {
        this.drinksData = drinksData;
        this.columnCount = columnCount;
        this.drawLeftToRight = drawLeftToRight;
    }
    async generateDrinkMenu() {
        this.pdf = await PDFDocument.create();
        let page = this.addNewPage();
        let { width, height } = page.getSize();
        const totalContentWidth = width - 2 * DrinkMenuGenerator.SIDE_MARGIN;
        const totalContentHeight = height - DrinkMenuGenerator.TOP_PAGE_MARGIN - DrinkMenuGenerator.BOTTOM_PAGE_MARGIN;
        // Create columns dynamically
        const columns = this.initializeColumns(page, totalContentWidth, totalContentHeight);
        const reorderedCategories = this.getReorderedCategories();
        if (this.drawLeftToRight) {
            this.drawCategoriesLeftToRight(columns, reorderedCategories);
        }
        else {
            this.drawCategoriesTopToBottom(columns, reorderedCategories);
        }
    }
    async savePdf() {
        const pdfBytes = await this.pdf.save();
        this.openPdfFileInNewTab(pdfBytes);
    }
    initializeColumns(page, totalContentWidth, totalContentHeight) {
        const columnWidth = (totalContentWidth - (this.columnCount - 1) * DrinkMenuGenerator.COLUMN_MARGIN) / this.columnCount;
        const columns = [];
        for (let i = 0; i < this.columnCount; i++) {
            const xStartPos = DrinkMenuGenerator.SIDE_MARGIN + i * (columnWidth + DrinkMenuGenerator.COLUMN_MARGIN);
            columns.push(new PageColumn(page, xStartPos, page.getHeight() - DrinkMenuGenerator.TOP_PAGE_MARGIN, columnWidth, totalContentHeight));
        }
        return columns;
    }
    drawCategoriesTopToBottom(columns, categories) {
        let currentColumnIndex = 0;
        for (const category of categories) {
            const expectedHeight = this.getExpectedHeightOfCategory(category.foodMenuItems.length);
            if (columns[currentColumnIndex].heightCursor - expectedHeight < columns[currentColumnIndex].yEndPos) {
                currentColumnIndex++;
                if (currentColumnIndex >= this.columnCount) {
                    const page = this.addNewPage();
                    columns = this.initializeColumns(page, columns[0].totalContentWidth * this.columnCount, columns[0].totalContentHeight);
                    currentColumnIndex = 0;
                }
            }
            if (this.columnCount > 1 && currentColumnIndex === 0) {
                this.drawSeparatorLines(columns);
            }
            this.drawCategoryTitle(columns[currentColumnIndex], category.name);
            columns[currentColumnIndex].heightCursor -= DrinkMenuGenerator.LINE_SPACE + 10;
            columns[currentColumnIndex].heightCursor = this.drawCategoryItems(columns[currentColumnIndex], category.foodMenuItems);
            columns[currentColumnIndex].heightCursor -= DrinkMenuGenerator.CATEGORY_SPACING;
        }
    }
    drawCategoriesLeftToRight(columns, categories) {
        let currentColumnIndex = -1;
        for (const category of categories) {
            const expectedHeight = this.getExpectedHeightOfCategory(category.foodMenuItems.length);
            // let currentColumnIndex = this.getColumnIndexWithMaxHeight(columns);
            currentColumnIndex = this.getRotationalColumnIndex(columns, currentColumnIndex);
            console.log('Current column index', currentColumnIndex, category.name);
            if (columns[currentColumnIndex].heightCursor - expectedHeight < columns[currentColumnIndex].yEndPos) {
                const page = this.addNewPage();
                const totalContentWidth = page.getWidth() - 2 * DrinkMenuGenerator.SIDE_MARGIN;
                const totalContentHeight = page.getHeight() - DrinkMenuGenerator.TOP_PAGE_MARGIN - DrinkMenuGenerator.BOTTOM_PAGE_MARGIN;
                columns = this.initializeColumns(page, totalContentWidth, totalContentHeight);
                // currentColumnIndex = this.getColumnIndexWithMaxHeight(columns);
                currentColumnIndex = 0;
                console.log('Changed page');
            }
            if (this.columnCount > 1) {
                this.drawSeparatorLines(columns);
            }
            this.drawCategoryTitle(columns[currentColumnIndex], category.name);
            columns[currentColumnIndex].heightCursor -= DrinkMenuGenerator.LINE_SPACE + 10;
            columns[currentColumnIndex].heightCursor = this.drawCategoryItems(columns[currentColumnIndex], category.foodMenuItems);
            columns[currentColumnIndex].heightCursor -= DrinkMenuGenerator.CATEGORY_SPACING;
        }
    }
    getRotationalColumnIndex(columns, currentColumnIndex) {
        if (currentColumnIndex >= this.columnCount - 1) {
            return 0;
        }
        return currentColumnIndex + 1;
    }
    getColumnIndexWithMaxHeight(columns) {
        return columns.reduce((maxIndex, column, index) => {
            return column.heightCursor > columns[maxIndex].heightCursor ? index : maxIndex;
        }, 0);
    }
    drawSeparatorLines(columns) {
        columns.forEach((column, index) => {
            if (index > 0) {
                const separatorX = column.xStartPos - DrinkMenuGenerator.COLUMN_MARGIN / 2;
                column.page.drawLine({
                    start: { x: separatorX, y: column.page.getHeight() - DrinkMenuGenerator.TOP_PAGE_MARGIN },
                    end: { x: separatorX, y: DrinkMenuGenerator.BOTTOM_PAGE_MARGIN },
                    thickness: DrinkMenuGenerator.SEPARATOR_LINE_WIDTH,
                    color: rgb(0, 0, 0),
                });
            }
        });
    }
    getReorderedCategories() {
        const expectedHeightOfCategories = new Map();
        for (const category of this.drinksData) {
            let expectedHeight = this.getExpectedHeightOfCategory(category.foodMenuItems.length);
            if (!expectedHeightOfCategories.has(expectedHeight)) {
                expectedHeightOfCategories.set(expectedHeight, category);
            }
            else {
                while (expectedHeightOfCategories.has(expectedHeight)) {
                    expectedHeight += 0.1;
                }
                expectedHeightOfCategories.set(expectedHeight, category);
            }
        }
        return Array.from(expectedHeightOfCategories.keys())
            .sort((a, b) => a - b).map(key => expectedHeightOfCategories.get(key));
    }
    getExpectedHeightOfCategory(totalItems) {
        return (totalItems * DrinkMenuGenerator.FONT_SIZE) +
            (totalItems * DrinkMenuGenerator.LINE_SPACE) +
            DrinkMenuGenerator.CATEGORY_FONT_SIZE;
    }
    drawCategoryTitle(column, name) {
        const xPos = this.getTextStartXPosition(column.totalContentWidth, name);
        column.page.drawText(name, {
            x: xPos + column.columnCursor,
            y: column.heightCursor,
            size: DrinkMenuGenerator.CATEGORY_FONT_SIZE,
        });
    }
    drawCategoryItems(column, items) {
        for (const item of items) {
            if (column.heightCursor < column.yEndPos + DrinkMenuGenerator.LINE_SPACE) {
                break; // This should switch column or page
            }
            const wrappedText = this.wrapText(item.name, DrinkMenuGenerator.MAX_TEXT_WIDTH, DrinkMenuGenerator.FONT_SIZE);
            wrappedText.forEach(line => {
                column.page.drawText(line, {
                    x: column.columnCursor + DrinkMenuGenerator.ITEM_NAME_OFFSET,
                    y: column.heightCursor,
                    size: DrinkMenuGenerator.FONT_SIZE,
                });
                column.heightCursor -= DrinkMenuGenerator.LINE_SPACE;
            });
            this.drawPrice(column, item.price, column.heightCursor + (wrappedText.length > 1 ? DrinkMenuGenerator.LINE_SPACE : DrinkMenuGenerator.LINE_SPACE / 2));
            column.heightCursor -= DrinkMenuGenerator.LINE_SPACE; // Space between items
        }
        return column.heightCursor;
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
    drawPrice(column, price, yPos) {
        const formattedPrice = price.includes("/") ? price : price;
        yPos = yPos || column.heightCursor;
        const priceX = column.columnCursor + column.totalContentWidth - DrinkMenuGenerator.PRICE_OFFSET;
        column.page.drawText(formattedPrice, {
            x: priceX,
            y: yPos + (DrinkMenuGenerator.LINE_SPACE / 2), // Align prices with text
            size: DrinkMenuGenerator.PRICE_FONT_SIZE,
        });
    }
    addNewPage() {
        const page = this.pdf.addPage();
        this.addHeaderToPdfPage(page, 'Alpin Curry');
        return page;
    }
    addHeaderToPdfPage(page, title) {
        const { width, height } = page.getSize();
        const headerHeight = 40;
        const headerFontSize = 28;
        const subHeaderFontSize = 20;
        page.drawText(title, {
            x: this.getTextStartXPosition(width, title, headerFontSize),
            y: height - headerHeight,
            size: headerFontSize,
            color: rgb(...this.toDecimalRGB(248, 178, 51)),
        });
        page.drawText('Drinks', {
            x: this.getTextStartXPosition(width, 'Drinks', subHeaderFontSize),
            y: height - headerHeight - headerFontSize + 5,
            size: subHeaderFontSize,
            color: rgb(...this.toDecimalRGB(248, 178, 51)),
        });
    }
    getTextStartXPosition(width, text, fontSize = DrinkMenuGenerator.CATEGORY_FONT_SIZE) {
        // return (width / 2) - (text.length * fontSize) / 4;
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
DrinkMenuGenerator.TOP_PAGE_MARGIN = 100;
DrinkMenuGenerator.BOTTOM_PAGE_MARGIN = 80;
DrinkMenuGenerator.SIDE_MARGIN = 10;
DrinkMenuGenerator.COLUMN_MARGIN = 5;
DrinkMenuGenerator.CATEGORY_FONT_SIZE = 18;
DrinkMenuGenerator.PRICE_FONT_SIZE = 10;
DrinkMenuGenerator.FONT_SIZE = 14;
DrinkMenuGenerator.LINE_SPACE = 12;
DrinkMenuGenerator.CATEGORY_SPACING = 20; // Space after each category
DrinkMenuGenerator.ITEM_NAME_OFFSET = 5; // Offset for item names from the column start
DrinkMenuGenerator.SEPARATOR_LINE_WIDTH = 1;
DrinkMenuGenerator.PRICE_OFFSET = 65; // Offset for prices
DrinkMenuGenerator.MAX_TEXT_WIDTH = 250; // Max width for text before wrapping
