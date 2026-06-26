<script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/12.11.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.11.0/firebase-analytics.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: "AIzaSyCwhGz1ufLS9kTuqxWCalivDuSqNB0oy44",
    authDomain: "calcium-petal-438817-i4.firebaseapp.com",
    projectId: "calcium-petal-438817-i4",
    storageBucket: "calcium-petal-438817-i4.firebasestorage.app",
    messagingSenderId: "693949650332",
    appId: "1:693949650332:web:d9a4580dbd2aad5cda2459",
    measurementId: "G-NFP9ZTG5Z3"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
</script>