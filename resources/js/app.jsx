import React, { useState } from "react";
import ReactDOM from "react-dom/client";

// 🔹 Usuarios
function Usuarios() {
    const [usuarios, setUsuarios] = useState(["Juan", "Ana"]);

    return (
        <div>
            <h2>Usuarios</h2>
            <button onClick={() => setUsuarios([...usuarios, "Nuevo usuario"])}>
                Agregar usuario
            </button>

            <ul>
                {usuarios.map((u, i) => (
                    <li key={i}>{u}</li>
                ))}
            </ul>
        </div>
    );
}

// 🔹 Toggle
function Toggle() {
    const [visible, setVisible] = useState(false);

    return (
        <div>
            <h2>Toggle</h2>
            <button onClick={() => setVisible(!visible)}>
                Mostrar / Ocultar
            </button>

            {visible && <p>👀 Ahora me ves</p>}
        </div>
    );
}

// 🔹 Formulario
function FormularioUsuarios() {
    const [nombre, setNombre] = useState("");
    const [usuarios, setUsuarios] = useState([]);

    const agregarUsuario = () => {
        if (nombre.trim() === "") return;

        setUsuarios([...usuarios, nombre]);
        setNombre("");
    };

    return (
        <div>
            <h2>Agregar usuario</h2>

            <input
                type="text"
                value={nombre}
                onChange={(e) => setNombre(e.target.value)}
                placeholder="Nombre..."
            />

            <button onClick={agregarUsuario}>
                Agregar
            </button>

            <ul>
                {usuarios.map((u, i) => (
                    <li key={i}>{u}</li>
                ))}
            </ul>
        </div>
    );
}

// ✅ Render individual
ReactDOM.createRoot(document.getElementById("usuarios")).render(<Usuarios />);
ReactDOM.createRoot(document.getElementById("formulario")).render(<FormularioUsuarios />);
ReactDOM.createRoot(document.getElementById("toggle")).render(<Toggle />);

