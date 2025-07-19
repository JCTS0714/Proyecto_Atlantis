<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>CRM Kanban Board</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">CRM</li>
    </ol>
  </section>

  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Gestión de Clientes y Oportunidades</h3>
      </div>

      <div class="box-body">
        <!-- Agregamos el CDN de Tailwind si no está cargado -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Estilos personalizados para el Kanban -->
        <style>
          .kanban-column {
              transition: all 0.3s ease;
          }
          .kanban-card {
              transition: transform 0.2s ease, box-shadow 0.2s ease;
          }
          .kanban-card:hover {
              transform: translateY(-2px);
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          }
          .kanban-card-moving {
              opacity: 0.5;
              transform: scale(0.95);
          }
          @media (max-width: 768px) {
              .kanban-container {
                  flex-direction: column;
              }
              .kanban-column {
                  width: 100% !important;
                  margin-bottom: 1rem;
              }
          }
        </style>

        <div class="max-w-7xl mx-auto">
          <div class="flex justify-between items-center mb-6">
            <div class="relative">
              <input type="text" id="new-card-text" placeholder="Nuevo contacto..." class="pl-4 pr-12 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <button id="add-card-btn" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition">+</button>
            </div>
          </div>

          <div class="kanban-container flex justify-between gap-4">
            <?php include "modulos/kanban-columnas.php"; ?>
          </div>
        </div>

      </div>

      <div class="box-footer">
        Kanban CRM - Seguimiento de Prospectos
      </div>
    </div>
  </section>
</div>

<script src="vistas/js/crm.js"></script>
