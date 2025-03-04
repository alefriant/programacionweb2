<?php
session_start();

function generarNotificacionOferta($forzar = false) {
  // Si no existe la sesión de notificaciones, la inicializamos
  if (!isset($_SESSION['notificaciones'])) {
      $_SESSION['notificaciones'] = [];
  }
  
  // Verificar si ya se mostró una notificación en esta sesión
  if (!isset($_SESSION['oferta_mostrada']) || $forzar) {
      // Lista de ofertas especiales
      $ofertas = [
          [
              'titulo' => '¡Oferta Especial!',
              'mensaje' => 'Viaje único a Galápagos $4,500,000',
              'tipo' => 'success'
          ],
          [
              'titulo' => '¡Oferta Limitada!',
              'mensaje' => 'Un mes de travesía en Tailandia $9,000,000',
              'tipo' => 'info'
          ],
          [
              'titulo' => '¡Experiencia Única!',
              'mensaje' => 'Visitas a las instalaciones de SpaceX y ver un lanzamiento de Starship $4,000,000',
              'tipo' => 'warning'
          ]
      ];
      
      // Seleccionar una oferta aleatoria
      $oferta_seleccionada = $ofertas[array_rand($ofertas)];
      
      // Guardar la notificación en la sesión
      $_SESSION['notificaciones'][] = $oferta_seleccionada;
      
      // Marcar que ya se mostró una oferta
      $_SESSION['oferta_mostrada'] = true;
      
      return true;
  }
  
  return false;
}

// Inicio Semana 6, Pregunta 1
// Formularios para gestión de vuelos y hoteles
// Conexión a la base de datos AGENCIA
function conectarBD() {
  $servername = "localhost";
  $username = "programacionweb2"; 
  $password = ".programacionweb2."; 
  $dbname = "AF_MyDatabase";
  
  $conn = new mysqli($servername, $username, $password, $dbname);
  
  if ($conn->connect_error) {
      die("Error de conexión: " . $conn->connect_error);
  }
  
  return $conn;
}


// Procesar formulario de vuelos
if (isset($_POST['guardar_vuelo'])) {
  $conn = conectarBD();
  
  // Obtener y sanitizar datos
  $nombre_vuelo = $conn->real_escape_string($_POST['nombre_vuelo']);
  $origen = $conn->real_escape_string($_POST['origen']);
  $destino = $conn->real_escape_string($_POST['destino']);
  $fecha_salida = $conn->real_escape_string($_POST['fecha_salida']);
  $hora_salida = $conn->real_escape_string($_POST['hora_salida']);
  $precio = floatval($_POST['precio']);
  
// Insertar vuelo
$sql = "INSERT INTO VUELO (origen, destino, fecha, plazas_disponibles, precio) 
        VALUES ('$origen', '$destino', '$fecha_salida', 100, $precio)";
  
  if ($conn->query($sql) === TRUE) {
      $mensaje_vuelo = "Vuelo registrado correctamente";
  } else {
      $mensaje_vuelo = "Error al registrar vuelo: " . $conn->error;
  }
  
  $conn->close();
}

// Procesar formulario de hoteles
if (isset($_POST['guardar_hotel'])) {
  $conn = conectarBD();
  
  // Obtener y sanitizar datos
  $nombre_hotel = $conn->real_escape_string($_POST['nombre_hotel']);
  $ubicacion = $conn->real_escape_string($_POST['ubicacion']);
  $categoria = intval($_POST['categoria']);
  $precio_noche = floatval($_POST['precio_noche']);
  $disponibilidad = isset($_POST['disponibilidad']) ? 1 : 0;
  
// Insertar hotel
$sql = "INSERT INTO HOTEL (nombre, ubicacion, habitaciones_disponibles, tarifa_noche) 
        VALUES ('$nombre_hotel', '$ubicacion', 50, $precio_noche)";
  
  if ($conn->query($sql) === TRUE) {
      $mensaje_hotel = "Hotel registrado correctamente";
  } else {
      $mensaje_hotel = "Error al registrar hotel: " . $conn->error;
  }
  
  $conn->close();
}

// Datos para los desplegables (opciones creativas)
$vuelos_disponibles = [
  ["id" => 1, "nombre" => "Volando con el Barón Rojo", "origen" => "Santiago", "destino" => "Isla de Pascua", "precio" => 2500000],
  ["id" => 2, "nombre" => "Capitán Phillips Adventure", "origen" => "Santiago", "destino" => "El Cairo", "precio" => 8500000],
  ["id" => 3, "nombre" => "Aladdin's Magic Carpet", "origen" => "Santiago", "destino" => "París", "precio" => 4800000],
  ["id" => 4, "nombre" => "Sinbad's Voyage", "origen" => "Santiago", "destino" => "Tailandia", "precio" => 8700000]
];

$hoteles_disponibles = [
  ["id" => 1, "nombre" => "Ali Baba y los 40 Ladrones Paradise", "ubicacion" => "Isla de Pascua", "categoria" => 5, "precio" => 350000],
  ["id" => 2, "nombre" => "El Palacio de las Mil y Una Noches", "ubicacion" => "El Cairo", "categoria" => 5, "precio" => 520000],
  ["id" => 3, "nombre" => "Torre Eiffel Dreams Lodge", "ubicacion" => "París", "categoria" => 4, "precio" => 480000],
  ["id" => 4, "nombre" => "Exotic Bangkok Golden Temple", "ubicacion" => "Tailandia", "categoria" => 5, "precio" => 390000]
];

// Fin Semana 6, Pregunta 1

// Inicio Semana 6, Pregunta 3
// Procesar formulario de reservas
if (isset($_POST['guardar_reserva'])) {
  $conn = conectarBD();
  
  // Obtener y sanitizar datos
  $id_cliente = intval($_POST['id_cliente']);
  $fecha_reserva = $conn->real_escape_string($_POST['fecha_reserva']);
  $id_vuelo = intval($_POST['id_vuelo']);
  $id_hotel = intval($_POST['id_hotel']);
  
  // Insertar reserva
  $sql = "INSERT INTO RESERVA (id_cliente, fecha_reserva, id_vuelo, id_hotel) 
          VALUES ($id_cliente, '$fecha_reserva', $id_vuelo, $id_hotel)";
  
  if ($conn->query($sql) === TRUE) {
      $mensaje_reserva = "Reserva registrada correctamente";
  } else {
      $mensaje_reserva = "Error al registrar reserva: " . $conn->error;
  }
  
  $conn->close();
}

// Función para obtener vuelos desde la base de datos
function obtenerVuelos() {
  $conn = conectarBD();
  $vuelos = [];
  
  $sql = "SELECT id_vuelo, origen, destino, fecha, precio FROM VUELO";
  $resultado = $conn->query($sql);
  
  if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
      $vuelos[] = $fila;
    }
  }
  
  $conn->close();
  return $vuelos;
}

// Función para obtener hoteles desde la base de datos
function obtenerHoteles() {
  $conn = conectarBD();
  $hoteles = [];
  
  $sql = "SELECT id_hotel, nombre, ubicacion, tarifa_noche FROM HOTEL";
  $resultado = $conn->query($sql);
  
  if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
      $hoteles[] = $fila;
    }
  }
  
  $conn->close();
  return $hoteles;
}

// Función para obtener las reservas
function obtenerReservas() {
  $conn = conectarBD();
  $reservas = [];
  
  $sql = "SELECT r.id_reserva, r.id_cliente, r.fecha_reserva, 
          v.origen, v.destino, v.precio as precio_vuelo, 
          h.nombre as nombre_hotel, h.ubicacion, h.tarifa_noche
          FROM RESERVA r
          JOIN VUELO v ON r.id_vuelo = v.id_vuelo
          JOIN HOTEL h ON r.id_hotel = h.id_hotel
          ORDER BY r.fecha_reserva DESC";
  
  $resultado = $conn->query($sql);
  
  if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
      $reservas[] = $fila;
    }
  }
  
  $conn->close();
  return $reservas;
}

// Función para obtener hoteles con más de dos reservas
function obtenerHotelesPopulares() {
  $conn = conectarBD();
  $hoteles_populares = [];
  
  $sql = "SELECT h.id_hotel, h.nombre, h.ubicacion, COUNT(r.id_reserva) as total_reservas
          FROM HOTEL h
          JOIN RESERVA r ON h.id_hotel = r.id_hotel
          GROUP BY h.id_hotel
          HAVING COUNT(r.id_reserva) > 2
          ORDER BY total_reservas DESC";
  
  $resultado = $conn->query($sql);
  
  if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
      $hoteles_populares[] = $fila;
    }
  }
  
  $conn->close();
  return $hoteles_populares;
}
// Fin Semana 6, Pregunta 3


























// Función para mostrar las notificaciones pendientes
function mostrarNotificaciones() {
  if (isset($_SESSION['notificaciones']) && !empty($_SESSION['notificaciones'])) {
      $html = '<div id="php-notificaciones">';
      
      foreach ($_SESSION['notificaciones'] as $key => $notificacion) {
          $html .= '<div class="notificacion notificacion-' . $notificacion['tipo'] . '">';
          $html .= '<span class="cerrar-notificacion" onclick="cerrarNotificacion(' . $key . ')">&times;</span>';
          $html .= '<h4>' . $notificacion['titulo'] . '</h4>';
          $html .= '<p>' . $notificacion['mensaje'] . '</p>';
          $html .= '</div>';
      }
      
      $html .= '</div>';
      
      // Limpiar las notificaciones después de mostrarlas
      $_SESSION['notificaciones'] = [];
      
      return $html;
  }
  
  return '';
}

// Generar una notificación al cargar la página
generarNotificacionOferta(true); // Corregido: se quitó el nombre del parámetro

// Si se envía una petición para cerrar una notificación
if (isset($_GET['cerrar_notificacion']) && is_numeric($_GET['cerrar_notificacion'])) {
  $indice = (int) $_GET['cerrar_notificacion'];
  if (isset($_SESSION['notificaciones'][$indice])) {
      unset($_SESSION['notificaciones'][$indice]);
      // Reindexar el array
      $_SESSION['notificaciones'] = array_values($_SESSION['notificaciones']);
  }
}

/**
 * Clase que representa un filtro interactivo para destinos y fechas de viaje
 */
class FiltroDestino {
    // Propiedades
    private $nombreHotel;
    private $ciudad;
    private $pais;
    private $fechaViaje;
    private $duracionViaje;
    
    /**
     * Constructor de la clase
     */
    public function __construct($nombreHotel = '', $ciudad = '', $pais = '', $fechaViaje = '', $duracionViaje = 0) {
        $this->nombreHotel = $nombreHotel;
        $this->ciudad = $ciudad;
        $this->pais = $pais;
        $this->fechaViaje = $fechaViaje;
        $this->duracionViaje = $duracionViaje;
    }
    
    /**
     * Método para establecer el nombre del hotel
     */
    public function setNombreHotel($nombreHotel) {
        $this->nombreHotel = $this->filtrarInput($nombreHotel);
        return $this;
    }
    
    /**
     * Método para obtener el nombre del hotel
     */
    public function getNombreHotel() {
        return $this->nombreHotel;
    }
    
    /**
     * Método para establecer la ciudad
     */
    public function setCiudad($ciudad) {
        $this->ciudad = $this->filtrarInput($ciudad);
        return $this;
    }
    
    /**
     * Método para obtener la ciudad
     */
    public function getCiudad() {
        return $this->ciudad;
    }
    
    /**
     * Método para establecer el país
     */
    public function setPais($pais) {
        $this->pais = $this->filtrarInput($pais);
        return $this;
    }
    
    /**
     * Método para obtener el país
     */
    public function getPais() {
        return $this->pais;
    }
    
    /**
     * Método para establecer la fecha de viaje
     */
    public function setFechaViaje($fechaViaje) {
        $this->fechaViaje = $fechaViaje;
        return $this;
    }
    
    /**
     * Método para obtener la fecha de viaje
     */
    public function getFechaViaje() {
        return $this->fechaViaje;
    }
    
    /**
     * Método para establecer la duración del viaje
     */
    public function setDuracionViaje($duracionViaje) {
        $this->duracionViaje = (int)$duracionViaje;
        return $this;
    }
    
    /**
     * Método para obtener la duración del viaje
     */
    public function getDuracionViaje() {
        return $this->duracionViaje;
    }
    
    /**
     * Método para filtrar y sanitizar datos de entrada
     */
    private function filtrarInput($datos) {
        $datos = trim($datos);
        $datos = stripslashes($datos);
        $datos = htmlspecialchars($datos);
        return $datos;
    }
    
    /**
     * Método para buscar destinos según los criterios actuales
     */
    public function buscarDestinos($destinos) {
        $resultados = [];
        
        foreach ($destinos as $destino) {
            $coincidencia = true;
            
            // Verificar coincidencias por cada criterio
            if (!empty($this->ciudad) && stripos($destino['ciudad'], $this->ciudad) === false) {
                $coincidencia = false;
            }
            
            if (!empty($this->pais) && stripos($destino['pais'], $this->pais) === false) {
                $coincidencia = false;
            }
            
            if (!empty($this->nombreHotel) && stripos($destino['hotel'], $this->nombreHotel) === false) {
                $coincidencia = false;
            }
            
            if (!empty($this->fechaViaje)) {
                $fechaDestino = strtotime($destino['fecha']);
                $fechaBusqueda = strtotime($this->fechaViaje);
                
                if ($fechaDestino < $fechaBusqueda) {
                    $coincidencia = false;
                }
            }
            
            if (!empty($this->duracionViaje) && $destino['duracion'] < $this->duracionViaje) {
                $coincidencia = false;
            }
            
            if ($coincidencia) {
                $resultados[] = $destino;
            }
        }
        
        return $resultados;
    }
    
    /**
     * Método para generar un formulario HTML de búsqueda
     */
    public function generarFormularioBusqueda() {
        $html = '<form method="post" action="" class="filtro-form">';
        $html .= '<div class="form-group">';
        $html .= '<label for="hotel">Hotel:</label>';
        $html .= '<input type="text" id="hotel" name="hotel" value="' . $this->nombreHotel . '">';
        $html .= '</div>';
        
        $html .= '<div class="form-group">';
        $html .= '<label for="ciudad">Ciudad:</label>';
        $html .= '<input type="text" id="ciudad" name="ciudad" value="' . $this->ciudad . '">';
        $html .= '</div>';
        
        $html .= '<div class="form-group">';
        $html .= '<label for="pais">País:</label>';
        $html .= '<input type="text" id="pais" name="pais" value="' . $this->pais . '">';
        $html .= '</div>';
        
        $html .= '<div class="form-group">';
        $html .= '<label for="fecha_viaje">Fecha de viaje:</label>';
        $html .= '<input type="date" id="fecha_viaje" name="fecha_viaje" value="' . $this->fechaViaje . '">';
        $html .= '</div>';
        
        $html .= '<div class="form-group">';
        $html .= '<label for="duracion">Duración (días):</label>';
        $html .= '<input type="number" id="duracion" name="duracion" min="1" value="' . $this->duracionViaje . '">';
        $html .= '</div>';
        
        $html .= '<div class="form-group">';
        $html .= '<input type="submit" name="buscar" value="Buscar">';
        $html .= '</div>';
        $html .= '</form>';
        
        return $html;
    }
    
    /**
     * Método para mostrar resultados de la búsqueda
     */
    public function mostrarResultados($resultados) {
        if (empty($resultados)) {
            return '<p>No se encontraron resultados con los criterios especificados.</p>';
        }
        
        $html = '<div class="resultados-busqueda">';
        $html .= '<h3>Resultados de la búsqueda</h3>';
        $html .= '<ul>';
        
        foreach ($resultados as $resultado) {
            $html .= '<li>';
            $html .= '<strong>Hotel:</strong> ' . $resultado['hotel'] . '<br>';
            $html .= '<strong>Ciudad:</strong> ' . $resultado['ciudad'] . '<br>';
            $html .= '<strong>País:</strong> ' . $resultado['pais'] . '<br>';
            $html .= '<strong>Fecha:</strong> ' . date('d/m/Y', strtotime($resultado['fecha'])) . '<br>';
            $html .= '<strong>Duración:</strong> ' . $resultado['duracion'] . ' días<br>';
            $html .= '<strong>Precio:</strong> $' . number_format($resultado['precio'], 0, ',', '.') . '<br>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Método para procesar los datos enviados por el formulario
     */
    public function procesarFormulario() {
        if (isset($_POST['buscar'])) {
            if (isset($_POST['hotel'])) {
                $this->setNombreHotel($_POST['hotel']);
            }
            
            if (isset($_POST['ciudad'])) {
                $this->setCiudad($_POST['ciudad']);
            }
            
            if (isset($_POST['pais'])) {
                $this->setPais($_POST['pais']);
            }
            
            if (isset($_POST['fecha_viaje'])) {
                $this->setFechaViaje($_POST['fecha_viaje']);
            }
            
            if (isset($_POST['duracion'])) {
                $this->setDuracionViaje($_POST['duracion']);
            }
            
            return true;
        }
        
        return false;
    }
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Lista de paquetes turísticos
$paquetes = [
    1 => ['nombre' => 'Isla de Pascua', 'precio' => 3000000],
    2 => ['nombre' => 'El Cairo', 'precio' => 9000000],
    3 => ['nombre' => 'París', 'precio' => 5000000]
];

// Verificar si se ha agregado un paquete al carrito
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $paqueteId = (int) $_GET['id'];
    if (isset($paquetes[$paqueteId])) {
        if (!isset($_SESSION['carrito'][$paqueteId])) {
            $_SESSION['carrito'][$paqueteId] = 1;
        } else {
            $_SESSION['carrito'][$paqueteId]++;
        }
    }
}

// Verificar si se ha eliminado un paquete del carrito
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $paqueteId = (int) $_GET['eliminar'];
    if (isset($_SESSION['carrito'][$paqueteId])) {
        unset($_SESSION['carrito'][$paqueteId]);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agencia de Viajes</title>
  <style>
    /* Estilos básicos para sustituir los archivos CSS faltantes */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }
    
    .formulario-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 20px;
    }
    
    .formulario {
      flex: 1;
      min-width: 300px;
      border: 1px solid #ccc;
      padding: 15px;
      border-radius: 5px;
    }
    
    .campo {
      margin-bottom: 10px;
    }
    
    .campo label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    
    .campo select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.campo input {
  width: calc(100% - 16px);
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.campo input[type="date"], 
.campo input[type="time"],
.campo input[readonly] {
  width: auto;
  min-width: 150px;
}
    
    .btn-submit {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 4px;
      cursor: pointer;
    }
    
    .opciones-container {
      margin-top: 20px;
    }
    
    .opcion-item {
      border: 1px solid #eee;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 4px;
    }
    
    .mensaje {
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 4px;
    }
    
    .mensaje-exito {
      background-color: #d4edda;
      color: #155724;
    }
    
    .mensaje-error {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    /* Estilos para notificaciones */
    #php-notificaciones {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
    }
    
    .notificacion {
      position: relative;
      padding: 15px;
      margin-bottom: 10px;
      border-radius: 5px;
      width: 300px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .notificacion-success {
      background-color: #d4edda;
      border-left: 4px solid #28a745;
    }
    
    .notificacion-info {
      background-color: #d1ecf1;
      border-left: 4px solid #17a2b8;
    }
    
    .notificacion-warning {
      background-color: #fff3cd;
      border-left: 4px solid #ffc107;
    }
    
    .cerrar-notificacion {
      position: absolute;
      top: 5px;
      right: 10px;
      cursor: pointer;
      font-size: 18px;
    }
    
    /* Estilos adicionales */
    .search-container, #filtros-container, #results-container, #paquetes-container, #carrito-container, #filtro-avanzado-container, #agregar-paquetes, #notificaciones-container {
      margin-bottom: 20px;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    
    h3 {
      margin-top: 0;
    }
    
    input, select, button {
      padding: 8px;
      margin-right: 5px;
    }
    
    button {
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin-right: 8px;
}

.checkbox-label span {
    margin-top: 2px;
}

.campo input[type="date"], 
.campo input[type="time"],
.campo input[type="number"],
.campo input[readonly] {
    width: auto;
    min-width: 150px;
}


  </style>
</head>

<body>
    <?php echo mostrarNotificaciones(); ?>

    <!-- Inicio Semana 6, Pregunta 1 -->
    <div class="formulario-container">
        <h1 style="text-align: center; width: 100%;">Gestión de Vuelos y Hoteles</h1>
        
        <!-- Formulario de Vuelos -->
        <div class="formulario">
            <h2>Registro de Vuelos</h2>
            
            <?php if (isset($mensaje_vuelo)): ?>
                <div class="mensaje <?php echo strpos($mensaje_vuelo, 'Error') !== false ? 'mensaje-error' : 'mensaje-exito'; ?>">
                    <?php echo $mensaje_vuelo; ?>
                </div>
            <?php endif; ?>
            
            <form id="formulario-vuelo" method="post" action="" onsubmit="return validarFormularioVuelo()">
                <div class="campo">
                    <label for="nombre_vuelo">Nombre del Vuelo:</label>
                    <select id="nombre_vuelo" name="nombre_vuelo">
                        <option value="">Seleccione un vuelo</option>
                        <?php foreach ($vuelos_disponibles as $vuelo): ?>
                            <option value="<?php echo $vuelo['nombre']; ?>"><?php echo $vuelo['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="campo">
                    <label for="origen">Origen:</label>
                    <input type="text" id="origen" name="origen" value="Santiago" readonly>
                </div>
                
                <div class="campo">
                    <label for="destino">Destino:</label>
                    <select id="destino" name="destino" onchange="actualizarPrecioVuelo()">
                        <option value="">Seleccione un destino</option>
                        <option value="Isla de Pascua">Isla de Pascua</option>
                        <option value="El Cairo">El Cairo</option>
                        <option value="París">París</option>
                        <option value="Tailandia">Tailandia</option>
                    </select>
                </div>
                
                <div class="campo">
                    <label for="fecha_salida">Fecha de Salida:</label>
                    <input type="date" id="fecha_salida" name="fecha_salida">
                </div>
                
                <div class="campo">
                    <label for="hora_salida">Hora de Salida:</label>
                    <input type="time" id="hora_salida" name="hora_salida">
                </div>
                
                <div class="campo">
                    <label for="precio">Precio (CLP):</label>
                    <input type="number" id="precio" name="precio" min="1">
                </div>
                
                <button type="submit" name="guardar_vuelo" class="btn-submit">Guardar Vuelo</button>
            </form>
            
            <div class="opciones-container">
                <h3>Vuelos Disponibles:</h3>
                <?php foreach ($vuelos_disponibles as $vuelo): ?>
                    <div class="opcion-item">
                        <h3><?php echo $vuelo['nombre']; ?></h3>
                        <p><strong>Origen:</strong> <?php echo $vuelo['origen']; ?></p>
                        <p><strong>Destino:</strong> <?php echo $vuelo['destino']; ?></p>
                        <p class="precio">Precio: $<?php echo number_format($vuelo['precio'], 0, ',', '.'); ?> CLP</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Formulario de Hoteles -->
        <div class="formulario">
            <h2>Registro de Hoteles</h2>
            
            <?php if (isset($mensaje_hotel)): ?>
                <div class="mensaje <?php echo strpos($mensaje_hotel, 'Error') !== false ? 'mensaje-error' : 'mensaje-exito'; ?>">
                    <?php echo $mensaje_hotel; ?>
                </div>
            <?php endif; ?>
            
            <form id="formulario-hotel" method="post" action="" onsubmit="return validarFormularioHotel()">
                <div class="campo">
                    <label for="nombre_hotel">Nombre del Hotel:</label>
                    <select id="nombre_hotel" name="nombre_hotel">
                        <option value="">Seleccione un hotel</option>
                        <?php foreach ($hoteles_disponibles as $hotel): ?>
                            <option value="<?php echo $hotel['nombre']; ?>"><?php echo $hotel['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="campo">
                    <label for="ubicacion">Ubicación:</label>
                    <select id="ubicacion" name="ubicacion" onchange="actualizarPrecioHotel()">
                        <option value="">Seleccione una ubicación</option>
                        <option value="Isla de Pascua">Isla de Pascua</option>
                        <option value="El Cairo">El Cairo</option>
                        <option value="París">París</option>
                        <option value="Tailandia">Tailandia</option>
                    </select>
                </div>
                
                <div class="campo">
                    <label for="categoria">Categoría (Estrellas):</label>
                    <select id="categoria" name="categoria">
                        <option value="3">3 Estrellas</option>
                        <option value="4">4 Estrellas</option>
                        <option value="5" selected>5 Estrellas</option>
                    </select>
                </div>
                
                <div class="campo">
                    <label for="precio_noche">Precio por noche (CLP):</label>
                    <select id="precio_noche" name="precio_noche">
                        <option value="">Seleccione un precio</option>
                        <option value="350000">$350,000</option>
                        <option value="390000">$390,000</option>
                        <option value="480000">$480,000</option>
                        <option value="520000">$520,000</option>
                        <option value="650000">$650,000</option>
                    </select>
                </div>
                
                <div class="campo">
                    <label class="checkbox-label">
                        <input type="checkbox" name="disponibilidad" checked>
                        <span>Disponible</span>
                    </label>
                </div>
                
                <button type="submit" name="guardar_hotel" class="btn-submit">Guardar Hotel</button>
            </form>
            
            <div class="opciones-container">
                <h3>Hoteles Disponibles:</h3>
                <?php foreach ($hoteles_disponibles as $hotel): ?>
                    <div class="opcion-item">
                        <h3><?php echo $hotel['nombre']; ?></h3>
                        <p><strong>Ubicación:</strong> <?php echo $hotel['ubicacion']; ?></p>
                        <p><strong>Categoría:</strong> <?php echo $hotel['categoria']; ?> Estrellas</p>
                        <p class="precio">Precio por noche: $<?php echo number_format($hotel['precio'], 0, ',', '.'); ?> CLP</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
   

    <script>
        // Validaciones con JavaScript
        function validarFormularioVuelo() {
            const nombreVuelo = document.getElementById('nombre_vuelo').value;
            const destino = document.getElementById('destino').value;
            const fechaSalida = document.getElementById('fecha_salida').value;
            const horaSalida = document.getElementById('hora_salida').value;
            const precio = document.getElementById('precio').value;
            
            if (!nombreVuelo || !destino || !fechaSalida || !horaSalida || !precio) {
                alert('Por favor, complete todos los campos del formulario de vuelo.');
                return false;
            }
            
            if (parseFloat(precio) <= 0) {
                alert('El precio debe ser un valor positivo.');
                return false;
            }
            
            return true;
        }
        
        function validarFormularioHotel() {
            const nombreHotel = document.getElementById('nombre_hotel').value;
            const ubicacion = document.getElementById('ubicacion').value;
            const precioNoche = document.getElementById('precio_noche').value;
            
            if (!nombreHotel || !ubicacion || !precioNoche) {
                alert('Por favor, complete todos los campos del formulario de hotel.');
                return false;
            }
            
            if (parseFloat(precioNoche) <= 0) {
                alert('El precio por noche debe ser un valor positivo.');
                return false;
            }
            
            return true;
        }
        
        // Actualizar precios según selección
        function actualizarPrecioVuelo() {
            const destino = document.getElementById('destino').value;
            const precioInput = document.getElementById('precio');
            
            // Precios según destino
            switch (destino) {
                case 'Isla de Pascua':
                    precioInput.value = 2500000;
                    break;
                case 'El Cairo':
                    precioInput.value = 8500000;
                    break;
                case 'París':
                    precioInput.value = 4800000;
                    break;
                case 'Tailandia':
                    precioInput.value = 8700000;
                    break;
                default:
                    precioInput.value = '';
            }
            
            // Actualizar también el nombre del vuelo según destino
            const nombreVueloSelect = document.getElementById('nombre_vuelo');
            
            for (let i = 0; i < nombreVueloSelect.options.length; i++) {
                nombreVueloSelect.options[i].selected = false;
            }
            
            const vuelos = <?php echo json_encode($vuelos_disponibles); ?>;
            
            for (let i = 0; i < vuelos.length; i++) {
                if (vuelos[i].destino === destino) {
                    // Buscar y seleccionar la opción correspondiente
                    for (let j = 0; j < nombreVueloSelect.options.length; j++) {
                        if (nombreVueloSelect.options[j].value === vuelos[i].nombre) {
                            nombreVueloSelect.options[j].selected = true;
                            break;
                        }
                    }
                    break;
                }
            }
        }
        
        function actualizarPrecioHotel() {
            const ubicacion = document.getElementById('ubicacion').value;
            const precioInput = document.getElementById('precio_noche');
            
            // Precios según ubicación
            switch (ubicacion) {
                case 'Isla de Pascua':
                    precioInput.value = 350000;
                    break;
                case 'El Cairo':
                    precioInput.value = 520000;
                    break;
                case 'París':
                    precioInput.value = 480000;
                    break;
                case 'Tailandia':
                    precioInput.value = 390000;
                    break;
                default:
                    precioInput.value = '';
            }
            
            // Actualizar también el nombre del hotel según ubicación
            const nombreHotelSelect = document.getElementById('nombre_hotel');
            
            for (let i = 0; i < nombreHotelSelect.options.length; i++) {
                nombreHotelSelect.options[i].selected = false;
            }
            
            const hoteles = <?php echo json_encode($hoteles_disponibles); ?>;
            
            for (let i = 0; i < hoteles.length; i++) {
                if (hoteles[i].ubicacion === ubicacion) {
                    // Buscar y seleccionar la opción correspondiente
                    for (let j = 0; j < nombreHotelSelect.options.length; j++) {
                        if (nombreHotelSelect.options[j].value === hoteles[i].nombre) {
                            nombreHotelSelect.options[j].selected = true;
                            break;
                        }
                    }
                    break;
                }
            }
        }
    </script>
    <!-- Fin Semana 6, Pregunta 2 -->


    <!-- Inicio Semana 6, Pregunta 3 -->
<!-- Formulario de Reservas y Consultas -->
<div class="formulario-container">
    <h1 style="text-align: center; width: 100%;">Gestión de Reservas</h1>
    
    <!-- Formulario de Reservas -->
    <div class="formulario">
        <h2>Registro de Nueva Reserva</h2>
        
        <?php if (isset($mensaje_reserva)): ?>
            <div class="mensaje <?php echo strpos($mensaje_reserva, 'Error') !== false ? 'mensaje-error' : 'mensaje-exito'; ?>">
                <?php echo $mensaje_reserva; ?>
            </div>
        <?php endif; ?>
        
        <form id="formulario-reserva" method="post" action="" onsubmit="return validarFormularioReserva()">
            <div class="campo">
                <label for="id_cliente">Número de Cliente:</label>
                <input type="number" id="id_cliente" name="id_cliente" min="1" required>
            </div>
            
            <div class="campo">
                <label for="fecha_reserva">Fecha de Reserva:</label>
                <input type="date" id="fecha_reserva" name="fecha_reserva" required>
            </div>
            
            <div class="campo">
                <label for="id_vuelo">Vuelo:</label>
                <select id="id_vuelo" name="id_vuelo" required>
                    <option value="">Seleccione un vuelo</option>
                    <?php 
                    $vuelos = obtenerVuelos();
                    foreach ($vuelos as $vuelo): 
                    ?>
                        <option value="<?php echo $vuelo['id_vuelo']; ?>">
                            <?php echo "Desde: {$vuelo['origen']} - Hasta: {$vuelo['destino']} - Fecha: {$vuelo['fecha']} - Precio: $" . number_format($vuelo['precio'], 0, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="campo">
                <label for="id_hotel">Hotel:</label>
                <select id="id_hotel" name="id_hotel" required>
                    <option value="">Seleccione un hotel</option>
                    <?php 
                    $hoteles = obtenerHoteles();
                    foreach ($hoteles as $hotel): 
                    ?>
                        <option value="<?php echo $hotel['id_hotel']; ?>">
                            <?php echo "{$hotel['nombre']} - {$hotel['ubicacion']} - Tarifa: $" . number_format($hotel['tarifa_noche'], 0, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" name="guardar_reserva" class="btn-submit">Guardar Reserva</button>
        </form>
    </div>
    
    <!-- Lista de Reservas -->
    <div class="formulario" style="max-height: 500px; overflow-y: auto;">
        <h2>Listado de Reservas</h2>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ID</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Cliente</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Fecha</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Origen-Destino</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Hotel</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Precio Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $reservas = obtenerReservas();
                if (empty($reservas)): 
                ?>
                <tr>
                    <td colspan="6" style="border: 1px solid #ddd; padding: 8px; text-align: center;">No hay reservas registradas</td>
                </tr>
                <?php 
                else:
                    foreach ($reservas as $reserva): 
                        $precio_total = $reserva['precio_vuelo'] + $reserva['tarifa_noche'];
                ?>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $reserva['id_reserva']; ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $reserva['id_cliente']; ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo "{$reserva['origen']} → {$reserva['destino']}"; ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo "{$reserva['nombre_hotel']} ({$reserva['ubicacion']})"; ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">$<?php echo number_format($precio_total, 0, ',', '.'); ?></td>
                </tr>
                <?php 
                    endforeach;
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Hoteles Populares (más de 2 reservas) -->
<div class="search-container" style="margin-top: 20px;">
    <h3>Hoteles con más de 2 reservas</h3>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Hotel</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Ubicación</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Total Reservas</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $hoteles_populares = obtenerHotelesPopulares();
            if (empty($hoteles_populares)): 
            ?>
            <tr>
                <td colspan="3" style="border: 1px solid #ddd; padding: 8px; text-align: center;">No hay hoteles con más de 2 reservas</td>
            </tr>
            <?php 
            else:
                foreach ($hoteles_populares as $hotel): 
            ?>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $hotel['nombre']; ?></td>
                <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $hotel['ubicacion']; ?></td>
                <td style="border: 1px solid #ddd; padding: 8px;"><?php echo $hotel['total_reservas']; ?></td>
            </tr>
            <?php 
                endforeach;
            endif; 
            ?>
        </tbody>
    </table>
</div>

<script>
    // Validación del formulario de reservas
    function validarFormularioReserva() {
        const idCliente = document.getElementById('id_cliente').value;
        const fechaReserva = document.getElementById('fecha_reserva').value;
        const idVuelo = document.getElementById('id_vuelo').value;
        const idHotel = document.getElementById('id_hotel').value;
        
        if (!idCliente || !fechaReserva || !idVuelo || !idHotel) {
            alert('Por favor, complete todos los campos del formulario de reserva.');
            return false;
        }
        
        if (parseInt(idCliente) <= 0) {
            alert('El ID del cliente debe ser un número positivo.');
            return false;
        }
        
        return true;
    }
</script>

    <!-- Fin Semana 6, Pregunta 3 -->



<?php echo mostrarNotificaciones(); ?>

  <div class="search-container">
    <h3>Busqueda de destinos disponibles</h3>        
    <input type="text" id="destinos" placeholder="Destino">
    <input type="date" id="fecha-viaje">
    <button onclick="buscar()">Buscar</button>
  </div>

  <div id="results-container">
    <h3>Disponibilidad de destinos</h3>
    <div id="resultados-dinamicos">
      <!-- Los resultados de la búsqueda se mostrarán aquí -->
    </div>
  </div>
  
    <div id="filtro-avanzado-container">
    <h3>Filtro Avanzado de Destinos</h3>
    <?php
    // Ejemplo de uso de la clase FiltroDestino
    // Crear instancia del filtro
    $filtro = new FiltroDestino();
    // Procesar formulario si se envió
    $filtro->procesarFormulario();
    // Datos de ejemplo para destinos disponibles
    $destinos_disponibles = [
        [
            'hotel' => 'Hotel Hanga Roa',
            'ciudad' => 'Isla de Pascua',
            'pais' => 'Chile',
            'fecha' => '2024-05-15',
            'duracion' => 7,
            'precio' => 3000000
        ],
        [
            'hotel' => 'Pyramids View Inn',
            'ciudad' => 'El Cairo',
            'pais' => 'Egipto',
            'fecha' => '2024-06-20',
            'duracion' => 10,
            'precio' => 9000000
        ],
        [
            'hotel' => 'Le Grand Hotel Paris',
            'ciudad' => 'París',
            'pais' => 'Francia',
            'fecha' => '2024-07-10',
            'duracion' => 5,
            'precio' => 5000000
        ],
        [
            'hotel' => 'Galápagos Sunset',
            'ciudad' => 'Islas Galápagos',
            'pais' => 'Ecuador',
            'fecha' => '2024-08-15',
            'duracion' => 9,
            'precio' => 4500000
        ],
        [
            'hotel' => 'Bangkok Oasis',
            'ciudad' => 'Bangkok',
            'pais' => 'Tailandia',
            'fecha' => '2024-09-01',
            'duracion' => 30,
            'precio' => 9000000
        ]
    ];
    // Obtener resultados de la búsqueda
    $resultados = $filtro->buscarDestinos($destinos_disponibles);
    // Para incluir en la página web:
    // 1. El formulario de búsqueda:
    echo $filtro->generarFormularioBusqueda();
    // 2. Los resultados (si se ha realizado una búsqueda):
    if ($filtro->procesarFormulario()) {
        echo $filtro->mostrarResultados($resultados);
    }
    ?>
  </div>

    
  <div id="filtros-container">
    <h3>Herramienta de filtro de paquetes</h3>
    <label>
      Disponibilidad:
      <select id="filtro-disponibilidad">
        <option value="">Todos</option>
        <option value="true">Disponible</option>
        <option value="false">No Disponible</option>
      </select>
    </label>
    <label>
      Precio máximo:
      <input type="text" id="filtro-precio" placeholder="Ingrese precio máximo">
    </label>
    <button onclick="filtrarPaquetes()">Aplicar Filtros</button>
  </div>

  <div id="paquetes-container">
    <h3>Paquetes Turisticos</h3>
    <div id="paquetes-lista">
      <!-- Los paquetes turísticos se mostrarán aquí -->
    </div>
  </div>

  <div id="carrito-container">
    <h3 style='text-align: center;'>Carrito de Compras</h3>
    <?php
    if (empty($_SESSION['carrito'])) {
        echo "<p style='text-align: center;'>El carrito está vacío.</p>";
    } else {
        echo "<ul style='text-align: center; list-style: none; padding: 0;'>";
        foreach ($_SESSION['carrito'] as $paqueteId => $cantidad) {
            $paqueteSeleccionado = $paquetes[$paqueteId];
            echo "<li style='margin-bottom: 10px;'>{$paqueteSeleccionado['nombre']} - Cantidad: {$cantidad}  
            <a href='?eliminar={$paqueteId}'>Eliminar</a></li>";
        }
        echo "</ul>";
    }
    ?>
</div>



  <script>
    /* Sistema de objetos para paquetes turísticos */
    function PaqueteTuristico(destino, precio, disponibilidad) {
      this.destino = destino;
      this.precio = precio;
      this.disponibilidad = disponibilidad;
      this.mostrarInfo = function () {
        // Formatea el precio con separadores de miles
        return `Destino: ${this.destino}, Precio: $${this.precio.toLocaleString("es-ES")}, Disponible: ${this.disponibilidad ? "Sí" : "No"}`;
      };
    }

    /* Instancias iniciales de paquetes turísticos */
    let paquetes = [
      new PaqueteTuristico("Isla de Pascua", 3000000, true),
      new PaqueteTuristico("El Cairo", 9000000, false),
      new PaqueteTuristico("Paris", 5000000, true)
    ];

    
    /* Muestra los paquetes turísticos en la UI */
    function mostrarPaquetes(lista = paquetes) {
  const contenedorLista = document.getElementById("paquetes-lista");

    /* Limpia únicamente los paquetes dinámicos */
    contenedorLista.innerHTML = "";

    /* Crea elementos para cada paquete utilizando un ciclo for */
    for (let i = 0; i < lista.length; i++) {
        const paqueteElemento = document.createElement("div");
        paqueteElemento.textContent = lista[i].mostrarInfo();
        contenedorLista.appendChild(paqueteElemento);
    }
    }

    /* Filtra paquetes según disponibilidad o precio */
    function filtrarPaquetes() {
      const disponibilidad = document.getElementById("filtro-disponibilidad").value;
      const precioMaximoInput = document.getElementById("filtro-precio").value;

      // Convertir el precio máximo con formato a número
      const precioMaximo = parseInt(precioMaximoInput.replace(/\./g, ""), 10);

      const paquetesFiltrados = paquetes.filter(paquete => {
        const coincideDisponibilidad = disponibilidad === "" || String(paquete.disponibilidad) === disponibilidad;
        const coincidePrecio = !precioMaximo || paquete.precio <= precioMaximo;
        return coincideDisponibilidad && coincidePrecio;
      });

      mostrarPaquetes(paquetesFiltrados);
    }

    /* Mostrar los paquetes turísticos al cargar la página */
    mostrarPaquetes();

    /* Formatear el campo de precio máximo con separadores de miles */
    const inputPrecio = document.getElementById("filtro-precio");

    inputPrecio.addEventListener("input", function () {
      // Eliminar cualquier formato previo
      let valorNumerico = inputPrecio.value.replace(/\./g, "");

      // Verificar si el valor es numérico y no está vacío
      if (!isNaN(valorNumerico) && valorNumerico !== "") {
        inputPrecio.value = Number(valorNumerico).toLocaleString("es-ES");
      } else {
        inputPrecio.value = "";
      }
    });

    /* Función para manejar notificaciones en tiempo real */
    function iniciarNotificaciones() {
      const contenedorNotificaciones = document.getElementById("notificaciones-container");
      const mensajes = [
        "Nueva oferta disponible para Isla de Pascua: 20% de descuento.",
        "Actualización: El paquete a París ahora está disponible.",
        "Oferta especial: El Cairo con 30% de descuento por tiempo limitado."
      ];
      let indice = 0;

      setInterval(() => {
        // Limpiar notificaciones anteriores
        contenedorNotificaciones.innerHTML = "<h3>Novedades!</h3>";

        // Mostrar el siguiente mensaje
        const notificacion = document.createElement("div");
        notificacion.textContent = mensajes[indice];
        contenedorNotificaciones.appendChild(notificacion);

        // Actualizar el índice
        indice = (indice + 1) % mensajes.length;
      }, 3000); // Cambiar notificación cada 3 segundos
    }


    /* Iniciar notificaciones al cargar la página */
    iniciarNotificaciones();

    /* Función para manejar la búsqueda */
    function buscar() {
        const destinos = ["Isla de Pascua", "El Cairo", "Paris"];
        const entradaDestino = document.getElementById("destinos").value.toLowerCase();
        const fechaViaje = document.getElementById("fecha-viaje").value;
        const encabezadoResultados = document.querySelector("#results-container h3");

    if (entradaDestino && fechaViaje) {
        const destinoFiltrado = destinos.find(destino =>
        destino.toLowerCase() === entradaDestino
        );

        if (destinoFiltrado) {
        // Actualiza el encabezado con el resultado
        encabezadoResultados.textContent = `Destino: ${destinoFiltrado}, Fecha: ${fechaViaje}`;
        } else {
        // Muestra un mensaje de error en el encabezado
        encabezadoResultados.textContent = "Destinos disponibles: Isla de Pascua, El Cairo o Paris.";
        }
    } else {
        // Muestra un mensaje de advertencia en el encabezado
        encabezadoResultados.textContent = "Ingrese un destino válido y seleccione una fecha.";
    }
    }

  </script>
  <div id="agregar-paquetes" style="text-align: center;">
    <h3>Agregar Paquetes al Carrito</h3>
    <div id="paquetes-carrito-lista">
      <!-- Aquí se mostrarán los paquetes con opción de agregar al carrito -->
    </div>
  </div>

  <script>
  function cerrarNotificacion(indice) {
    window.location.href = `?cerrar_notificacion=${indice}`;
  }
</script>

  <script>
    function generarBotonesAgregar() {
      const contenedorCarritoLista = document.getElementById("paquetes-carrito-lista");
      contenedorCarritoLista.innerHTML = "";

      paquetes.forEach((paquete, index) => {
        const paqueteElemento = document.createElement("div");
        paqueteElemento.innerHTML = `${paquete.destino} - $${paquete.precio.toLocaleString('es-ES')} <a href='?id=${index + 1}'>Agregar al carrito</a>`;
        contenedorCarritoLista.appendChild(paqueteElemento);
      });
    }
    
    generarBotonesAgregar();
  </script>
  <div id="notificaciones-container">
    <h3>Novedades!</h3>
    <!-- Las notificaciones aparecerán aquí dinámicamente -->
  </div>

  <script>
    function iniciarNotificaciones() {
      const contenedorNotificaciones = document.getElementById("notificaciones-container");
      const mensajes = [
        "Nueva oferta disponible para Isla de Pascua: 20% de descuento.",
        "Actualización: El paquete a París ahora está disponible.",
        "Oferta especial: El Cairo con 30% de descuento por tiempo limitado."
      ];
      let indice = 0;

      setInterval(() => {
        contenedorNotificaciones.innerHTML = "<h3>Novedades!</h3>";
        const notificacion = document.createElement("div");
        notificacion.textContent = mensajes[indice];
        contenedorNotificaciones.appendChild(notificacion);
        indice = (indice + 1) % mensajes.length;
      }, 3000);
    }
    
    document.addEventListener("DOMContentLoaded", iniciarNotificaciones);
  </script>
</body>

</html>