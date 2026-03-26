<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen General</title>
    <link rel="stylesheet" href="expedienteG.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="logo-utn.ico" type="image/x-icon">
    @vite('resources/js/app.jsx')
</head>
<body>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="{{ asset('imgs/utn.png') }}" alt="Logo Institucional" class="custom-sidebar-logo">
                <span>Servicios Escolares</span>
            </div>
            <nav class="sidebar-nav">
                <a href="/gestion-alumnos" class="nav-item"><i class="fa-solid fa-user-graduate"></i> Gestión de Alumnos</a>
                <a href="/gestion-tutores" class="nav-item"><i class="fa-solid fa-chalkboard-user"></i> Gestión de Tutores</a>
                <a href="/gestion-admins" class="nav-item"><i class="fa-solid fa-shield-halved"></i> Administradores</a>
            </nav>
            <div class="sidebar-footer">
                <a href="{{ asset('/Registro') }}" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Salir</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin+General&background=10504B&color=fff&size=100" alt="Foto del admin" class="profile-img">
                    <div class="student-info">
                        <h1>Administración General</h1>
                        <p class="student-id"><i class="fa-solid fa-shield-halved"></i> Rol: <strong>Servicios Escolares</strong></p>
                        <p class="student-career"><i class="fa-solid fa-university"></i> Universidad Tecnológica de Nayarit</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card progress-card full-width">
                    <h3><i class="fa-solid fa-chart-line"></i> Estadísticas Generales</h3>
                    <div class="stats-container" style="margin-bottom: 0;">
                        <div class="stat-box">
                            <span class="stat-value">1,250</span>
                            <span class="stat-label">Total Alumnos</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-value">21</span>
                            <span class="stat-label">Total Tutores</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-value">15</span>
                            <span class="stat-label">Total Administradores</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fa-solid fa-laptop-code"></i> Alumnos por Carrera</h3>
                    <ul class="info-list">
                        <li><strong>Desarrollo de Software:</strong> <span>450 alumnos</span></li>
                        <li><strong>Mecatrónica:</strong> <span>320 alumnos</span></li>
                        <li><strong>Negocios Internacionales:</strong> <span>280 alumnos</span></li>
                        <li><strong>Gastronomía:</strong> <span>200 alumnos</span></li>
                    </ul>
                </div>

                 <div class="card">
                    <h3><i class="fa-solid fa-chalkboard-user"></i> Tutores por Carrera</h3>
                    <ul class="info-list">
                        <li><strong>Desarrollo de Software:</strong> <span>4 Tutores</span></li>
                        <li><strong>Mecatrónica:</strong> <span>3 Tutores</span></li>
                        <li><strong>Negocios Internacionales:</strong> <span>5 Tutores</span></li>
                        <li><strong>Gastronomía:</strong> <span>9 Tutores</span></li>
                    </ul>
                </div>

                <div class="card">
                    <h3><i class="fa-solid fa-database"></i> Respaldos Automatizados</h3>
                    
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <button id="prevMonth" class="btn-icon" type="button"><i class="fa-solid fa-chevron-left"></i></button>
                            <h4 id="monthYear"></h4>
                            <button id="nextMonth" class="btn-icon" type="button"><i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                        <ul class="calendar-weeks">
                            <li>Dom</li><li>Lun</li><li>Mar</li><li>Mié</li><li>Jue</li><li>Vie</li><li>Sáb</li>
                        </ul>
                        <ul class="calendar-days" id="calendarDays"></ul>
                    </div>

                    <form action="#" method="POST" style="margin-top: 15px; border-top: 1px dashed var(--border-color); padding-top: 15px;">
                        <div class="form-group">
                            <label style="font-size: 13px;">Fecha seleccionada:</label>
                            <input type="date" id="selectedDate" name="backup_date" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label style="font-size: 13px;">Hora del respaldo:</label>
                            <input type="time" name="backup_time" class="form-control" value="02:00" required>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px; display: flex; justify-content: center; gap: 8px;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Agendar Respaldo
                        </button>
                    </form>

                </div>
            </div> </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthYear = document.getElementById("monthYear");
            const calendarDays = document.getElementById("calendarDays");
            const prevMonth = document.getElementById("prevMonth");
            const nextMonth = document.getElementById("nextMonth");
            const inputDate = document.getElementById("selectedDate");

            let date = new Date();
            let currYear = date.getFullYear();
            let currMonth = date.getMonth();

            const months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

            const renderCalendar = () => {
                let firstDayofMonth = new Date(currYear, currMonth, 1).getDay();
                let lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate();
                let lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay();
                let lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate();
                
                let liTag = "";

                for (let i = firstDayofMonth; i > 0; i--) {
                    liTag += `<li class="inactive">${lastDateofLastMonth - i + 1}</li>`;
                }

                for (let i = 1; i <= lastDateofMonth; i++) {
                    let isToday = i === date.getDate() && currMonth === new Date().getMonth() && currYear === new Date().getFullYear() ? "active" : "";
                    liTag += `<li class="${isToday}" data-day="${i}">${i}</li>`;
                }

                for (let i = lastDayofMonth; i < 6; i++) {
                    liTag += `<li class="inactive">${i - lastDayofMonth + 1}</li>`;
                }

                monthYear.innerText = `${months[currMonth]} ${currYear}`;
                calendarDays.innerHTML = liTag;

                // Funcionalidad de click en los días
                document.querySelectorAll(".calendar-days li:not(.inactive)").forEach(item => {
                    item.addEventListener("click", (e) => {
                        // Quitar clase active a todos y ponersela al clickeado
                        document.querySelectorAll(".calendar-days li").forEach(el => el.classList.remove("active"));
                        e.target.classList.add("active");
                        
                        // Formatear fecha para el input (YYYY-MM-DD)
                        let day = e.target.getAttribute('data-day').padStart(2, '0');
                        let month = (currMonth + 1).toString().padStart(2, '0');
                        inputDate.value = `${currYear}-${month}-${day}`;
                    });
                });
                
                // Setear fecha inicial si estamos en el mes actual
                if(currMonth === new Date().getMonth() && currYear === new Date().getFullYear()){
                    let today = date.getDate().toString().padStart(2, '0');
                    let month = (currMonth + 1).toString().padStart(2, '0');
                    inputDate.value = `${currYear}-${month}-${today}`;
                }
            }

            renderCalendar();

            prevMonth.addEventListener("click", () => {
                currMonth = currMonth - 1;
                if(currMonth < 0 || currMonth > 11) {
                    date = new Date(currYear, currMonth, new Date().getDate());
                    currYear = date.getFullYear();
                    currMonth = date.getMonth();
                } else {
                    date = new Date();
                }
                renderCalendar();
            });

            nextMonth.addEventListener("click", () => {
                currMonth = currMonth + 1;
                if(currMonth < 0 || currMonth > 11) {
                    date = new Date(currYear, currMonth, new Date().getDate());
                    currYear = date.getFullYear();
                    currMonth = date.getMonth();
                } else {
                    date = new Date();
                }
                renderCalendar();
            });
        });
    </script>
</body>
</html>