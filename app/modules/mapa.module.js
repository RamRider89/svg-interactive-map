class MapaModule{
    constructor() { 
        this.id = null;
        this.positionModule = new PositionModule();
    }
    
    setMapaCampusControls(id) {
        this.id = id;
        this.setEvents();
        this.setControls();
    }

    setEvents() {
        console.log("-> Set positions events.");
        const container = document.getElementById("imageContainer");
        const herraduras = document.querySelectorAll("g.herradura");
        const delta = 6;
        let startX, startY, click = false;
        let paths = [];

        container.addEventListener('mousedown', (event) => {
            startX = event.pageX;
            startY = event.pageY;
        });

        container.addEventListener('mouseup', (event) => {
            const diffX = Math.abs(event.pageX - startX);
            const diffY = Math.abs(event.pageY - startY);
            (diffX < delta && diffY < delta) ? click = true : click = false;
        });

        herraduras.forEach(item => { paths.push(item.querySelectorAll("g.posicion")); });

        paths.forEach(grupo => {
            grupo.forEach(element => element.addEventListener("click", () => { (click) ? this.positionModule.setCampusPosition(element.id) : 0; }));
            
        });

        //loadDataCampusGeneral();
    }

    setControls() {
        console.log("-> Set controls.");

        setTimeout(() => {

            let campusPanZoom;
            let options = {
                panEnabled: true,
                zoomEnabled: true,
                dblClickZoomEnabled: true,
                mouseWheelZoomEnabled: true,
                zoomScaleSensitivity: 0.5,
                minZoom: 0.5,
                maxZoom: 10,
                fit: true,
                center: true,
                controlIconsEnabled: false,
                beforeZoom: (ev) => { },
                onZoom: (ev) => { },
                beforePan: (ev) => { },
                onPan: (ev) => { },
                onUpdatedCTM: (ev) => { }
            };

            try {
                campusPanZoom = svgPanZoom('#viewport', options);
            } catch (error) {
                console.warn(error + " : Reload SVG Controls");
                campusPanZoom.destroy();
                setControls();
                return;
            }

            if (!campusPanZoom) {
                campusPanZoom.destroy();
                console.warn('Reload SVG Controls');
                setControls();
                return;
            }

            document.getElementById('zoom-in').addEventListener('click', (ev) => {
                ev.preventDefault();
                document.getElementById("imageContainer").style.cursor = "zoom-in";
                campusPanZoom.zoomIn();
            });

            document.getElementById('zoom-out').addEventListener('click', (ev) => {
                ev.preventDefault();
                document.getElementById("imageContainer").style.cursor = "zoom-out";
                campusPanZoom.zoomOut();
            });

            document.getElementById('reset').addEventListener('click', (ev) => {
                ev.preventDefault();
                campusPanZoom.zoom(0.5);
            });


            setTimeout(() => {
                campusPanZoom.fit();
                campusPanZoom.center();
                // campusPanZoom.zoom(0.5);

                // function customPanBy(zoom) { // {x: 1, y: 2}
                //     var animationTime = 500 // ms
                //         , animationStepTime = 20 // one frame per 30 ms
                //         , animationSteps = animationTime / animationStepTime    // 25
                //         , animationStep = 0 // ++
                //         , intervalID = null
                //         , zoom = zoom - 0.02  //0.005

                //     intervalID = setInterval(function () {
                //         if (animationStep++ < animationSteps) {
                //             campusPanZoom.zoomBy(zoom)
                //         } else {
                //             clearInterval(intervalID)
                //         }
                //     }, animationStepTime)
                // }

            }, 1500);

        }, 1500);
    }

}