-- schema.sql
CREATE DATABASE IF NOT EXISTS servicio_social CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE servicio_social;

-- Usuarios del sistema (administrador, docente, estudiante)

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  clave VARCHAR(255) NOT NULL,
  nombre VARCHAR(100),
  email VARCHAR(120),
  rol ENUM('administrador','docente','estudiante','gestor') NOT NULL DEFAULT 'estudiante',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  gestor_access TINYINT(1) DEFAULT 0 -- New column for gestor access
) ENGINE=InnoDB;

-- Estudiantes
CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  document VARCHAR(30) NOT NULL UNIQUE,
  first_name VARCHAR(60) NOT NULL,
  last_name VARCHAR(60) NOT NULL,
  grade VARCHAR(20) NOT NULL,
  email VARCHAR(120),
  phone VARCHAR(30),
  usuario VARCHAR(50) NOT NULL UNIQUE,
  clave VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Docentes
CREATE TABLE teachers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  subject VARCHAR(120),
  email VARCHAR(120),
  usuario VARCHAR(50) NOT NULL UNIQUE,
  clave VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Acuerdos de acompañamiento (Placement)
CREATE TABLE placements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  teacher_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  days_of_week JSON NOT NULL,
  status ENUM('pending','active','completed','cancelled') DEFAULT 'pending',
  total_hours_cached DECIMAL(6,2) DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pl_stu FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT fk_pl_tea FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Registro de horas
CREATE TABLE hour_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  placement_id INT NOT NULL,
  log_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  hours DECIMAL(5,2) NOT NULL,
  notes VARCHAR(255),
  approved TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_log_pl FOREIGN KEY (placement_id) REFERENCES placements(id) ON DELETE CASCADE,
  CONSTRAINT ck_time CHECK (end_time > start_time)
) ENGINE=InnoDB;

-- Índices útiles
CREATE INDEX idx_students_name ON students(last_name, first_name);
CREATE INDEX idx_teachers_name ON teachers(name);
CREATE INDEX idx_placements_status ON placements(status);
CREATE INDEX idx_logs_date ON hour_logs(log_date);

-- Datos de ejemplo (opcional)
INSERT INTO students (document, first_name, last_name, grade, email, phone, usuario, clave)
VALUES
('10001', 'Ana', 'Pérez', '11-1', 'ana.perez@colegio.edu', '3001112233', 'estudiante1', 'estudiante123'),
('10002', 'Luis', 'Gómez', '11-2', 'luis.gomez@colegio.edu', '3002223344', 'estudiante2', 'estudiante456');

INSERT INTO teachers (name, subject, email, usuario, clave)
VALUES
('Carolina Ruiz', 'Ciencias', 'carolina.ruiz@colegio.edu', 'docente1', 'docente123'),
('David Mora', 'Matemáticas', 'david.mora@colegio.edu', 'docente2', 'docente456');

INSERT INTO roles (usuario, clave, nombre, email, rol) VALUES
('admin', 'admin123', 'Administrador', 'admin@colegio.edu', 'administrador');