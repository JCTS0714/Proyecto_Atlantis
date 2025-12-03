-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 03-12-2025 a las 16:32:08
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u652153415_atlantisdb`
--

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `password`, `perfil`, `foto`, `estado`, `ultimo_login`, `fecha`, `sesion_token`, `sesion_expira`, `remember_token`, `remember_expires`) VALUES
(28, 'carlos', 'juan', '$2y$10$F25sju.D127s9AuhlvgxcuUtYteAV3PgKgl5oia1HNUsKmDJFIwtm', 'Admnistrador', 'vistas/img/usuarios/juan/955.jpg', 1, '2025-12-03 08:57:46', '2025-11-12 21:38:48', '255eb26919834cca4d382ce22356861a0efa690c1e4625009d04cbfbb743c00f', '2026-01-02 08:57:46', NULL, '2025-10-15 23:14:12'),
(37, 'ATLANTIS', 'ATLANTIS', '$2a$07$asxx54ahjppf45sd87a5auueb9VJEGI60Yrgx3jaiY6i/L2Iu66BK', 'Admnistrador', 'vistas/img/usuarios/ATLANTIS/452.png', 1, '2025-11-17 11:14:40', '2025-11-12 21:38:49', '1ff66bcabef0d133871a8b23bcab18328bea39c7e8bd54644d754a8c1fb1517f', '2025-12-17 11:14:40', NULL, NULL),
(38, 'Julio', 'julio', '$2a$07$asxx54ahjppf45sd87a5aumUskocpQucMnvwsUt.aC6WLWGcLNcY6', 'Admnistrador', 'vistas/img/usuarios/julio/106.jpg', 1, '2025-10-28 09:26:54', '2025-10-28 15:23:24', NULL, '2025-11-27 09:26:54', NULL, NULL),
(39, 'Luna', 'luna', '$2a$07$asxx54ahjppf45sd87a5aueBIdG/K13eg/g.ypaD/KXlsopxll2fC', 'Admnistrador', 'vistas/img/usuarios/luna/718.jpg', 1, '2025-12-03 09:24:41', '2025-11-13 14:26:32', '4989eba1d21a9e2c4b5b0bd395c73c7e28166071ab072b5c861ef813261b837f', '2026-01-02 09:24:41', NULL, NULL),
(40, 'GARAY', 'garay', '$2a$07$asxx54ahjppf45sd87a5ausSl3CojO/j2XbJ1Jfxgq32KQUtUA.kC', 'Admnistrador', 'vistas/img/usuarios/garay/723.jpg', 1, '2025-11-14 12:35:58', '2025-11-13 17:10:05', NULL, '2025-12-14 12:35:58', NULL, NULL),
(41, 'Atlantis', 'admin', '$2a$07$asxx54ahjppf45sd87a5auia1mVQcfGnHbxgvsMbUgrxcI9fwgAFS', 'Admnistrador', '', 1, '2025-12-02 17:33:50', '2025-11-08 14:30:21', NULL, '2026-01-01 17:33:50', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
