import * as THREE from './three.module.js';
import { STLLoader } from './STLLoader.js';

document.addEventListener('DOMContentLoaded', function() {
    // ID de l'élément contenant le modèle 3D
    let chaiseContainer = document.getElementById('chaise-container');

    // Initialisation de la scène
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, chaiseContainer.offsetWidth/chaiseContainer.offsetHeight, 1, 1000);
    camera.position.z = 2.5;
    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(chaiseContainer.offsetWidth, chaiseContainer.offsetHeight);
    renderer.setClearAlpha(0);
    chaiseContainer.appendChild(renderer.domElement);

    const light = new THREE.DirectionalLight(0xffffff, 1);
    light.position.set(5, 5, 5).normalize();
    scene.add(light);

    const loader = new STLLoader();
    let model;

    const pivot = new THREE.Object3D();
    scene.add(pivot);

    loader.load(chaiseContainer.dataset.model, function(geometry) {  
        const material = new THREE.MeshPhongMaterial({ color: 0x258cbe, specular: 0x111111, shininess: 200 });
        model = new THREE.Mesh(geometry, material);
        model.position.set(0,0,0);
        pivot.add(model); // 👈 on ajoute le modèle DANS le pivot
    }, undefined, function(error) {
        console.error(error);
    });


    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }
    animate();
    
    // Event listener for mouse movement
    let isDragging = false;
    let allowedToDrag = true; // Variable to control dragging
    let phase = 0; // Variable to control the phase of the animation
    let treeContainer = document.getElementById('three-background');

    let canvasWidth = treeContainer.offsetWidth;
    let canvasHeight = treeContainer.offsetHeight;
    window.addEventListener('resize', () => {
        if(phase >= 1){
            canvasWidth = treeContainer.offsetWidth/2; // Réduire la taille du canvas pour l'animation
        }
        camera.aspect = canvasWidth / canvasHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(canvasWidth, canvasHeight);
    });
    window.addEventListener('mousedown', (e) => {
        isDragging = true;
        if(phase == 0) {
            // setTimeout(() => {
            //     phase = 1; // Passer à la phase 1 après 5 secondes
            //     autoRotate();
            // }, 5000); // 10 seconds delay before allowing dragging
        }
    });
    window.addEventListener('mouseup', (e) => {
        isDragging = false;
        if (phase == 1) {
            autoRotate(); // Appeler la fonction d'auto-rotation
        }
    });

    window.addEventListener('mousemove', (e) => {
        if (isDragging && model && allowedToDrag) {
            const deltaX = Math.round((e.movementX * 0.01)*100)/100;
            const deltaY = Math.round((e.movementY * 0.01)*100)/100;

            pivot.rotation.y += deltaX; // rotation horizontale
            model.rotation.x += deltaY; // rotation verticale locale

            console.log(`Rotation X: ${model.rotation.x}, Rotation Y: ${pivot.rotation.y}`);
        }
    });

    function autoRotate() {  
        allowedToDrag = false; // Désactiver le drag
        let coordX = chaiseContainer.dataset.x;
        let coordY = chaiseContainer.dataset.y;
        let rotateAuto = setInterval(() => {
            if (model) {
                let acceleration = 0.006; // Ajuster la vitesse de rotation
                let vitesseX = acceleration * (Math.abs(model.rotation.x-coordX)+1);
                let vitesseY = acceleration * (Math.abs(pivot.rotation.y-coordY)+1); 
                if (model.rotation.x > coordX) {
                    model.rotation.x = Math.round((model.rotation.x - vitesseX) * 100)/100; // Ajuster la vitesse de rotation
                }
                if (model.rotation.x < coordX) {
                    model.rotation.x = Math.round((model.rotation.x + vitesseX) * 100)/100; // Ajuster la vitesse de rotation
                }
                if (pivot.rotation.y > coordY) {
                    pivot.rotation.y = Math.round((pivot.rotation.y - vitesseY) * 100)/100; // Ajuster la vitesse de rotation
                }
                if (pivot.rotation.y < coordY) {
                    pivot.rotation.y = Math.round((pivot.rotation.y + vitesseY) * 100)/100; // Ajuster la vitesse de rotation
                } 
                if (model.rotation.x == coordX && pivot.rotation.y == coordY) {
                    allowedToDrag = true; // Réactiver le drag après la rotation
                    if(treeContainer && !treeContainer.classList.contains('active')) {
                        canvasWidth /= 2; // Réduire la taille du canvas pour l'animation
                        camera.aspect = canvasWidth / canvasHeight;
                        camera.updateProjectionMatrix();
                        renderer.setSize(canvasWidth, canvasHeight);
                        treeContainer.classList.add('active'); // Ajouter la classe active
                        document.getElementById('ushi-audio').play(); // Jouer le son
                    }
                    clearInterval(rotateAuto); // Arrêter l'intervalle une fois la position atteinte
                }
            }
        }, 1000 / 60); // 60 FPS

    }

    function rotateX(angle) {
        if (model) {
            model.rotation.x += angle;
        }
    }
    function rotateY(angle) {
        if (pivot) {
            pivot.rotation.y += angle;
        }
    }

});
