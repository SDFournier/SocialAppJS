CREATE USER 'contactos'@'localhost' IDENTIFIED BY 'contactos';
GRANT USAGE ON *.* TO 'contactos'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
CREATE DATABASE IF NOT EXISTS `contactos`;
GRANT ALL PRIVILEGES ON `contactos`.* TO 'contactos'@'localhost'; 


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `contactos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `apellido` text COLLATE utf8_spanish_ci NOT NULL,
  `email` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `domicilio` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_de_nacimiento` date DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `compartido` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id`, `nombre`, `apellido`, `email`, `domicilio`, `fecha_de_nacimiento`, `id_usuario`, `compartido`) VALUES
(1, 'Ricardo Daniel', 'Bertoni', 'rdb@caindependiente.com', 'Bochini 751', '1955-03-14', 1, 1),
(2, 'Diego Armando', 'Maradona', 'dam@maradona.com', 'Segurola y Habana', '1960-10-30', 1, 0),
(3, 'Ricardo Enrique', 'Bochini', 'bocha_rojo@gmail.com', 'L. Legido 1010', '1954-01-25', 1, 1),
(4, 'Sergio \"Kun\"', 'Agüero', 'kun@guero.com', 'Barcelona 2021', '2000-12-12', 1, 0),
(5, 'Jorge Luis', 'Burruchaga', 'burru@gmail.com', 'Mexico 1986', '1962-10-09', 1, 1),
(6, 'Gabriel', 'Milito', 'gmilito@elverdadero.com', 'Milito 123', '1995-12-05', 1, 0),
(7, 'Lisandro', 'Lopez', 'mufandro@loepz.com', 'Pechardi 666', '2000-02-01', 3, 1);

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `tema` varchar(255) NOT NULL,
  `texto` varchar(255) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL
  
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `tema`, `texto`, `id_usuario`, `fecha`) VALUES
(1, 'Primera Prueba', 'Hola como va', '1', '2022-02-21 11:03:10' )
-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_destinatarios`
--

CREATE TABLE `mensajes_destinatarios` (
  `id_mensaje` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
  
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mensajes_destinatarios`
--

INSERT INTO `mensajes_destinatarios` (`id_mensaje`, `id_usuario`) VALUES
(1, 3)
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens`
--

CREATE TABLE `tokens` (
  `token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tokens`
--

INSERT INTO `tokens` (`token`) VALUES
('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJleHBpcmEiOjE2MzU5ODgwNDgsImlkIjozfQ.YiSuH91O9sFE5odN8FayZl1HoaJXlRGOYyPheOvSCniLANhmVCQ5_CcQrVeP4_88aVEdnK94IDJQloTdTVSglA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) NULL,
  `clave` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `email`, `clave`) VALUES
(1, 'pepe@pepe.com', '123'),
(3, 'lolo@lolo.com', 'abc');

--
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes`
--

CREATE TABLE `imagenes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `path_imagen` varchar(255) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `imagenes`
--
INSERT INTO `imagenes` (`id_usuario`, `path_imagen`) VALUES
( 1, 'imagenes/gato.jpg'),
( 1, 'imagenes/homero.jpg'),
( 1, 'imagenes/pinguin.png'),
( 3, 'imagenes/gato.jpg'),
( 3, 'imagenes/pinguin.png');





-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

  --

--

  -- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

-- Filtros para la tabla `mensajes_destinatarios`
--
ALTER TABLE `mensajes_destinatarios`
  ADD CONSTRAINT `mensajes_destinatarios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

ALTER TABLE `mensajes_destinatarios`
  ADD CONSTRAINT `mensajes_destinatarios_ibfk_2` FOREIGN KEY (`id_mensaje`) REFERENCES `mensajes` (`id`) ON DELETE CASCADE;
COMMIT;




--
-- Filtros para la tabla `imagenes`
--
ALTER TABLE `imagenes`
  ADD CONSTRAINT `imagenes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
