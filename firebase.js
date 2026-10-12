// FIREBASE CONFIGURATION & INITIALIZATION
const firebaseConfig = {
  apiKey: "AIzaSyAQUuZKn74gHHvNST4X5dUqWLlq583IKnI",
  authDomain: "smm-panel-59a8b.firebaseapp.com",
  databaseURL: "https://smm-panel-59a8b-default-rtdb.firebaseio.com",
  projectId: "smm-panel-59a8b",
  storageBucket: "smm-panel-59a8b.firebasestorage.app",
  messagingSenderId: "859076508323",
  appId: "1:859076508323:web:2f663fe5dcf9ee3d9edd16",
  measurementId: "G-Y1PFSPV1NS"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Export Auth & DB globally
const auth = firebase.auth();
const db = firebase.database();

// ImgBB API Key (Global)
const IMGBB_KEY = "172dabb7ec93ead71d6b9e5631f3155d";

console.log("Firebase Connected via firebase.js");