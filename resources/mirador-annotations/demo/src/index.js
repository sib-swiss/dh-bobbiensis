import mirador from 'mirador/dist/es/src/index';
import annotationPlugins from '../../src';
import AnnotationStoreAdapter from '../../src/AnnotationStoreAdapter';

const SERVER_URL = '..';


const urlParams = new URLSearchParams(window.location.search);
const manuscript = urlParams.get('manuscript') ? urlParams.get('manuscript') : 'VL%201%20Mark%2015:46b-16:8';
const manifestURL = `/iiif/${manuscript}/manifest.json`;

const config = {
  annotation: {
    adapter: (canvasId) => new AnnotationStoreAdapter(canvasId, SERVER_URL),
    orageAnnotations: false, // display annotation JSON export button
  },
  id: 'demo',
  window: {
    defaultSideBarPanel: 'annotations',
    sideBarOpenByDefault: true,
  },
  windows: [{
    loadedManifest: manifestURL,
  }],
};

mirador.viewer(config, [...annotationPlugins]);
