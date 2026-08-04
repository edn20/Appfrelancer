<?php

namespace Classes;

class Paginacion
{
    public int $paginaActual;
    public int $registrosPorPagina;
    public int $totalRegistros;
    public array $opcionesPorPagina;

    public function __construct(
        $paginaActual = 1,
        $registrosPorPagina = 10,
        $totalRegistros = 0,
        $opcionesPorPagina = [5, 10, 15, 20]
    ) {
        $this->opcionesPorPagina = $opcionesPorPagina;

        $this->paginaActual = filter_var($paginaActual, FILTER_VALIDATE_INT) ?: 1;

        if ($this->paginaActual < 1) {
            $this->paginaActual = 1;
        }

        $this->registrosPorPagina = filter_var($registrosPorPagina, FILTER_VALIDATE_INT) ?: 10;

        if (!in_array($this->registrosPorPagina, $this->opcionesPorPagina)) {
            $this->registrosPorPagina = 10;
        }

        $this->totalRegistros = (int) $totalRegistros;
    }

    public function offset(): int
    {
        return ($this->paginaActual - 1) * $this->registrosPorPagina;
    }

    public function totalPaginas(): int
    {
        if ($this->totalRegistros === 0) {
            return 1;
        }

        return (int) ceil($this->totalRegistros / $this->registrosPorPagina);
    }

    public function paginaAnterior()
    {
        $anterior = $this->paginaActual - 1;

        return $anterior >= 1 ? $anterior : false;
    }

    public function paginaSiguiente()
    {
        $siguiente = $this->paginaActual + 1;

        return $siguiente <= $this->totalPaginas() ? $siguiente : false;
    }

    public function inicio(): int
    {
        if ($this->totalRegistros === 0) {
            return 0;
        }

        return (($this->paginaActual - 1) * $this->registrosPorPagina) + 1;
    }

    public function fin(): int
    {
        return min($this->paginaActual * $this->registrosPorPagina, $this->totalRegistros);
    }

    public function crearUrl($pagina = null, $registrosPorPagina = null): string
    {
        $query = $_GET;

        $query['page'] = $pagina ?? $this->paginaActual;
        $query['per_page'] = $registrosPorPagina ?? $this->registrosPorPagina;

        return '?' . http_build_query($query);
    }

    public function paginaFueraDeRango(): bool
    {
        return $this->totalRegistros > 0 && $this->paginaActual > $this->totalPaginas();
    }

    public function selectorPorPagina(string $formId): string
    {
        $html = '<div class="paginacion-control">';
        $html .= '<label for="per_page">Mostrar</label>';

        $html .= '<select id="per_page" name="per_page" form="' . $formId . '">';

        foreach ($this->opcionesPorPagina as $opcion) {
            $selected = (int) $this->registrosPorPagina === (int) $opcion ? 'selected' : '';

            $html .= '<option value="' . $opcion . '" ' . $selected . '>';
            $html .= $opcion;
            $html .= '</option>';
        }

        $html .= '</select>';
        $html .= '<span>registros</span>';
        $html .= '</div>';

        return $html;
    }

    public function paginacion(string $texto = 'registros'): string
    {
        $html = '<div class="tabla-footer">';

        $html .= '<p>';
        $html .= 'Mostrando ' . $this->inicio() . ' a ' . $this->fin() . ' de ' . $this->totalRegistros . ' ' . $texto;
        $html .= '</p>';

        if ($this->totalPaginas() > 1) {
            $html .= '<div class="paginacion">';

            if ($this->paginaAnterior()) {
                $html .= '<a class="paginacion__boton" href="' . $this->crearUrl($this->paginaAnterior()) . '">';
                $html .= '<i class="bi bi-chevron-left"></i>';
                $html .= '</a>';
            } else {
                $html .= '<span class="paginacion__boton paginacion__boton--disabled">';
                $html .= '<i class="bi bi-chevron-left"></i>';
                $html .= '</span>';
            }

            for ($i = 1; $i <= $this->totalPaginas(); $i++) {
                if ($i === (int) $this->paginaActual) {
                    $html .= '<span class="paginacion__numero paginacion__numero--activo">';
                    $html .= $i;
                    $html .= '</span>';
                } else {
                    $html .= '<a class="paginacion__numero" href="' . $this->crearUrl($i) . '">';
                    $html .= $i;
                    $html .= '</a>';
                }
            }

            if ($this->paginaSiguiente()) {
                $html .= '<a class="paginacion__boton" href="' . $this->crearUrl($this->paginaSiguiente()) . '">';
                $html .= '<i class="bi bi-chevron-right"></i>';
                $html .= '</a>';
            } else {
                $html .= '<span class="paginacion__boton paginacion__boton--disabled">';
                $html .= '<i class="bi bi-chevron-right"></i>';
                $html .= '</span>';
            }

            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}
