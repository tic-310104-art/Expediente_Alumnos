import { initializeApp } from "https://www.gstatic.com/firebasejs/12.11.0/firebase-app.js";
import { getStorage, ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/12.11.0/firebase-storage.js";

const firebaseConfig = {
    apiKey: "AIzaSyCwhGz1ufLS9kTuqxWCalivDuSqNB0oy44",
    authDomain: "calcium-petal-438817-i4.firebaseapp.com",
    projectId: "calcium-petal-438817-i4",
    storageBucket: "calcium-petal-438817-i4.firebasestorage.app",
    messagingSenderId: "693949650332",
    appId: "1:693949650332:web:d9a4580dbd2aad5cda2459",
    measurementId: "G-NFP9ZTG5Z3"
};

const app = initializeApp(firebaseConfig);
const storage = getStorage(app);

export { storage, ref, uploadBytes, getDownloadURL };
