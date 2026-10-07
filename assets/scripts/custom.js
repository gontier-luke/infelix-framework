$(document).ready(function() {
    const titleImage = document.getElementById("title-image");
    const titleCircle = document.querySelector(".title-circle");
    const movingCircle = document.getElementById("circle");
    if (titleImage && titleCircle && movingCircle) {
        createConvergingLetterRectangles(titleImage, titleCircle);

        $(titleImage).on("mousedown", (event) => {
            event.preventDefault();
            $(titleCircle).addClass("active");
        });

        $(titleImage).on("mouseup", (event) => {
            event.preventDefault();
            $(titleCircle).removeClass("active");
        });
        let hasScrolled = false;
        var lastScrollTop = 0;
        lastScrollTop = $(window).scrollTop();

        $(window).on("scroll", (event) => {
            if (!hasScrolled && $(window).scrollTop() > lastScrollTop) {
                circleStartFollowingMouse(movingCircle);
                hasScrolled = true;
            }
        });
    }
});
async function createConvergingLetterRectangles(svg, titleCircle) {
    const SVG_NS = "http://www.w3.org/2000/svg";
    const overlay = document.createElementNS(SVG_NS, "g");
    overlay.setAttribute("pointer-events", "none");
    overlay.setAttribute("aria-hidden", "true");
    svg.appendChild(overlay);

    const svgPoint = svg.createSVGPoint();
    const rootMatrix = svg.getScreenCTM();
    if (!rootMatrix) return;

    let coordinates = "210,-180";
    if (titleCircle) {
        coordinates = `${titleCircle.cx.baseVal.value},${titleCircle.cy.baseVal.value}`;
    }
    const point = coordinates.replace(/\s+/g, "").split(",").map(Number);
    let target;
    if (point.length === 2 && point.every(Number.isFinite)) {
        target = { x: point[0], y: point[1] };
    } else {
        const box = svg.viewBox && svg.viewBox.baseVal;
        target = box && box.width ?
            { x: box.x + box.width / 2, y: box.y + box.height / 2 } :
            { x: svg.getBoundingClientRect().width / 2, y: svg.getBoundingClientRect().height / 2 };
    }

    const toRootPoint = (element, x, y) => {
        svgPoint.x = x;
        svgPoint.y = y;
        const matrix = element.getScreenCTM();
        const screenPoint = svgPoint.matrixTransform(matrix);
        return screenPoint.matrixTransform(rootMatrix.inverse());
    };

    const sampleVisibleContour = async element => {
        const viewBox = svg.viewBox && svg.viewBox.baseVal;
        const bounds = viewBox && viewBox.width && viewBox.height ? viewBox : {
            x: 0,
            y: 0,
            width: svg.getBoundingClientRect().width,
            height: svg.getBoundingClientRect().height
        };
        const scale = 1;
        const canvas = document.createElement("canvas");
        canvas.width = Math.ceil(bounds.width * scale);
        canvas.height = Math.ceil(bounds.height * scale);
        const context = canvas.getContext("2d", { willReadFrequently: true });
        const clone = svg.cloneNode(true);

        const sourceNodes = [svg, ...svg.querySelectorAll("*")];
        const cloneNodes = [clone, ...clone.querySelectorAll("*")];
        const copiedProperties = [
            "font-family", "font-size", "font-stretch", "font-style",
            "font-variant", "font-weight", "letter-spacing", "word-spacing",
            "text-anchor", "dominant-baseline", "alignment-baseline", "fill",
            "fill-opacity", "stroke", "stroke-opacity",
            "stroke-linecap", "stroke-linejoin", "stroke-miterlimit",
            "paint-order", "opacity", "display", "visibility"
        ];
        sourceNodes.forEach((source, index) => {
            const destination = cloneNodes[index];
            if (!destination) return;
            const computed = getComputedStyle(source);
            copiedProperties.forEach(property => {
                destination.style.setProperty(property, computed.getPropertyValue(property));
            });
        });

        const sourceTspans = Array.from(svg.querySelectorAll("tspan"));
        const selectedIndex = sourceTspans.indexOf(element);

        Array.from(clone.querySelectorAll("tspan")).forEach((tspan, index) => {
            if (index !== selectedIndex) {
                tspan.style.setProperty("visibility", "hidden", "important");
            }
        });
        clone.setAttribute("width", canvas.width);
        clone.setAttribute("height", canvas.height);
        clone.setAttribute("preserveAspectRatio", "none");

        const image = new Image();
        const loaded = new Promise((resolve, reject) => {
            image.onload = resolve;
            image.onerror = reject;
        });
        const url = URL.createObjectURL(new Blob([
            new XMLSerializer().serializeToString(clone)
        ], { type: "image/svg+xml" }));
        image.src = url;
        try {
            await loaded;
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
        } finally {
            URL.revokeObjectURL(url);
        }

        const pixels = context.getImageData(0, 0, canvas.width, canvas.height).data;
        const alphaAt = (x, y) => x < 0 || y < 0 || x >= canvas.width || y >= canvas.height ? 0 :
            pixels[(y * canvas.width + x) * 4 + 3];
        const points = [];

        const scanStep = 4;
        for (let y = 0; y < canvas.height; y+= scanStep) {
            for (let x = 0; x < canvas.width; x+= scanStep) {
                if (alphaAt(x, y) > 20 && [
                    alphaAt(x - 1, y), alphaAt(x + 1, y),
                    alphaAt(x, y - 1), alphaAt(x, y + 1)
                ].some(alpha => alpha <= 20)) {
                    points.push(toRootPoint(svg, bounds.x + x / scale, bounds.y + y / scale));
                }
            }
        }
        return points;
    };

    const sampleContour = element => {
        if (element.tagName.toLowerCase() === "tspan" &&
            typeof element.getNumberOfChars === "function") {
            const points = [];
            const count = element.getNumberOfChars();
            const step = 2;

            for (let index = 0; index < count; index++) {
                let box;
                try {
                    box = element.getExtentOfChar(index);
                } catch (error) {
                    continue;
                }
                if (!box || !box.width || !box.height) continue;

                const sides = [
                    [box.x, box.y, box.x + box.width, box.y],
                    [box.x + box.width, box.y, box.x + box.width, box.y + box.height],
                    [box.x + box.width, box.y + box.height, box.x, box.y + box.height],
                    [box.x, box.y + box.height, box.x, box.y]
                ];
                sides.forEach(([x1, y1, x2, y2]) => {
                    const length = Math.hypot(x2 - x1, y2 - y1);
                    for (let distance = 0; distance < length; distance += step) {
                        const ratio = distance / length;
                        points.push(toRootPoint(element,
                            x1 + (x2 - x1) * ratio,
                            y1 + (y2 - y1) * ratio));
                    }
                });
            }
            return points;
        }

        if (typeof element.getTotalLength === "function" &&
            typeof element.getPointAtLength === "function") {
            const length = element.getTotalLength();
            const step = 2;
            const points = [];
            for (let distance = 0; distance <= length; distance += step) {
                const p = element.getPointAtLength(Math.min(distance, length));
                points.push(toRootPoint(element, p.x, p.y));
            }
            return points;
        }
        return [];
    };
    
    if (document.fonts && document.fonts.ready) await document.fonts.ready;
    const visibleContours = new Map();
    for (const element of svg.querySelectorAll("tspan")) {
        if (getComputedStyle(element).stroke !== "none") {
            try {
                visibleContours.set(element, await sampleVisibleContour(element));
            } catch (error) {
                console.warn("Fallback utilisé pour", element, error);
                visibleContours.set(element, sampleContour(element));
            }
        }
    }

    Array.from(svg.querySelectorAll("tspan"))
        .filter(element => getComputedStyle(element).stroke !== "none")
        .forEach(element => {
            (visibleContours.get(element) || sampleContour(element)).forEach(async start => {
                const polygon = document.createElementNS(SVG_NS, "polygon");
                const color = getComputedStyle(element).stroke;
                const angle = Math.atan2(target.y - start.y, target.x - start.x);
                const cos = Math.cos(angle);
                const sin = Math.sin(angle);
                const dropShape = [
                    [-8, 0],  // pointe
                    [-2, -4],
                    [3, -3],
                    [6, 0],  // arrière
                    [3, 3],
                    [-2, 4]
                ];
                const points = dropShape.map(([x, y]) => {
                    const rotatedX = x * cos - y * sin;
                    const rotatedY = x * sin + y * cos;
                    return `${start.x + rotatedX},${start.y + rotatedY}`;
                }).join(" ");
                polygon.setAttribute("width", "0");
                polygon.setAttribute("height", "0");
                polygon.setAttribute("points", points);
                polygon.setAttribute("fill", color);
                polygon.style.opacity = "0";
                overlay.appendChild(polygon);
                
                const duration = 900 + Math.random() * 700;
                const delay = Math.random() * 500;
                const lift = 4 + Math.random() * 5;
                const liftX = -cos * lift;
                const liftY = -sin * lift;
                const targetX = target.x - start.x;
                const targetY = target.y - start.y;
                await polygon.animate([
                        { offset: 0, transform: "translate(0px, 0px)", opacity: 0 },
                        { offset: 0.10, transform: `translate(${liftX}px, ${liftY}px)`, opacity: 0.95 },
                        { offset: 0.20, transform: `translate(${liftX * 0.35}px, ${liftY * 0.35}px)`, opacity: 0.80 },
                        { offset: 1, transform: `translate(${targetX}px, ${targetY}px)`, opacity: 0.15 }
                    ], { duration, delay, easing: "cubic-bezier(.2,.8,.2,1)", fill: "both" });

                setTimeout(() => {
                    overlay.removeChild(polygon);
                }, duration + delay + 100);
            });
        });

    setTimeout(() => {
        circleFollowMouse();
    }, 2100);

    let timerBeforeFollow;
    function circleFollowMouse() {
        
        if (!svg) return;
        let hasMoved = false;
        $(svg).on("mousemove", e => {
            if (!hasMoved) {
                hasMoved = true;
                $(titleCircle).addClass("moving");
            }
            timerBeforeFollow = setTimeout(() => {
                updateCirclePosition(svg, e);
            }, 100);
        });
    }

    function updateCirclePosition(svg, event) {
        let point = svg.createSVGPoint();
        point.x = event.clientX;
        point.y = event.clientY;
        let cursorPoint = point.matrixTransform(svg.getScreenCTM().inverse());
        let deffX = cursorPoint.x - titleCircle.cx.baseVal.value;
        let deffY = cursorPoint.y - titleCircle.cy.baseVal.value;
        let newX = cursorPoint.x ;
        let newY = cursorPoint.y ;
        titleCircle.setAttribute("cx", newX);
        titleCircle.setAttribute("cy", newY);
    }
}

function circleStartFollowingMouse(movingCircle) {

    if (!movingCircle) return;
    $(movingCircle).addClass("moving");

    $(document).on("mousemove", e => {
        let newX = e.clientX - movingCircle.offsetWidth / 2;
        let newY = e.clientY - movingCircle.offsetHeight / 2;
        movingCircle.style.left = `${newX}px`;
        movingCircle.style.top = `${newY}px`;
    });
}