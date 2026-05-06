#!/bin/bash
for module in page administracion; do
  echo "=== Módulo: $module ==="
  for controller in $(find $module/Controllers -name "*Controller.php" | sed 's|.*/||' | sed 's|Controller.php||'); do
    controller_file="$module/Controllers/${controller}Controller.php"
    # Capitaliza primera letra usando tr y tail
    capitalized=$(echo $controller | tr '[:lower:]' '[:upper:]' | head -c1)$(echo $controller | tail -c+2)
    model_file="$module/Models/DbTable/${capitalized}.php"
    view_dir="$module/Views/$controller"
    view_files=$(find $view_dir -name "*.php" 2>/dev/null)
    
    # Funciones en Controller
    funcs_ctrl=$(grep -c "^\s*\(public\|protected\|private\)\s*function" "$controller_file" 2>/dev/null || echo 0)
    lines_ctrl=$(grep -v '^\s*$' "$controller_file" | wc -l 2>/dev/null | awk '{print $1}' || echo 0)
    
    # Funciones en Model
    funcs_model=$(grep -c "^\s*\(public\|protected\|private\)\s*function" "$model_file" 2>/dev/null || echo 0)
    lines_model=$(grep -v '^\s*$' "$model_file" | wc -l 2>/dev/null | awk '{print $1}' || echo 0)
    
    # Líneas en Views (funciones = 0)
    lines_views=0
    for view in $view_files; do
      lines_views=$((lines_views + $(grep -v '^\s*$' "$view" | wc -l | awk '{print $1}')))
    done
    
    total_funcs=$((funcs_ctrl + funcs_model))
    total_lines=$((lines_ctrl + lines_model + lines_views))
    
    echo "- ${controller}Controller.php:"
    echo "  Controller: Funciones $funcs_ctrl, Líneas $lines_ctrl"
    echo "  Model: Funciones $funcs_model, Líneas $lines_model"
    echo "  Views: Funciones 0, Líneas $lines_views"
    echo "  Total: Funciones $total_funcs, Líneas $total_lines"
  done
done