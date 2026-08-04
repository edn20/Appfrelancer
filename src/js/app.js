document.addEventListener('DOMContentLoaded', () => {
    const usuarioMenu = document.querySelector('.usuario-menu');
    const usuarioBtn = document.querySelector('#usuarioMenuBtn');

    const notificaciones = document.querySelector('.notificaciones');
    const notificacionesBtn = document.querySelector('#notificacionesBtn');

    const sidebar = document.querySelector('.sidebar');
    const btnSidebar = document.querySelector('#btnSidebar');

    const botonesLogout = document.querySelectorAll('.js-cerrar-sesion');

    if (usuarioMenu && usuarioBtn) {
        usuarioBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            usuarioMenu.classList.toggle('usuario-menu--open');

            if (notificaciones) {
                notificaciones.classList.remove('notificaciones--open');
            }
        });

        usuarioMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    if (notificaciones && notificacionesBtn) {
        notificacionesBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            notificaciones.classList.toggle('notificaciones--open');

            if (usuarioMenu) {
                usuarioMenu.classList.remove('usuario-menu--open');
            }
        });

        notificaciones.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    document.addEventListener('click', () => {
        if (usuarioMenu) {
            usuarioMenu.classList.remove('usuario-menu--open');
        }

        if (notificaciones) {
            notificaciones.classList.remove('notificaciones--open');
        }
    });

    if (sidebar && btnSidebar) {
        btnSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar--open');
        });
    }

    botonesLogout.forEach((boton) => {
        boton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const url = boton.getAttribute('href');

            Swal.fire({
                title: '¿Cerrar sesión?',
                text: 'Se cerrará tu sesión actual y volverás al login.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0057ff',
                cancelButtonColor: '#64748b',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    const formulariosEliminarCliente = document.querySelectorAll('.js-eliminar-cliente');

    formulariosEliminarCliente.forEach(formulario => {
        formulario.addEventListener('submit', (e) => {
            e.preventDefault();

            const nombreCliente = formulario.querySelector('input[name="nombre"]').value;

            Swal.fire({
                title: '¿Eliminar cliente?',
                text: `El cliente ${nombreCliente} se eliminará. Seguro quiere continuar?.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    formulario.submit();
                }
            });
        });
    });

    const formulariosEliminarProyecto = document.querySelectorAll('.js-eliminar-proyecto');

    formulariosEliminarProyecto.forEach(formulario => {
        formulario.addEventListener('submit', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const nombreProyecto = formulario.querySelector('input[name="nombre"]').value;
            const estadoProyecto = formulario.querySelector('input[name="estado"]').value;

            if (estadoProyecto !== 'Entregado' && estadoProyecto !== 'Cancelado') {
                Swal.fire({
                    title: 'No se puede eliminar',
                    text: 'Solo puedes eliminar proyectos que estén Entregados o Cancelados.',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#0057ff'
                });

                return;
            }

            Swal.fire({
                title: '¿Eliminar proyecto?',
                text: `El proyecto "${nombreProyecto}" se eliminará. Seguro quiere continuar?.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    formulario.submit();
                }
            });
        });
    });

    const formulariosEliminarTarea = document.querySelectorAll('.js-eliminar-tarea');

    formulariosEliminarTarea.forEach(formulario => {
        formulario.addEventListener('submit', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const nombreTarea = formulario.querySelector('input[name="nombre"]').value;
            const estadoTarea = formulario.querySelector('input[name="estado"]').value;

            if (estadoTarea !== 'Anulada') {
                Swal.fire({
                    title: 'No se puede eliminar',
                    text: 'Solo puedes eliminar tareas que estén en estado Anulada. Las tareas completadas se conservan como historial.',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#0057ff'
                });

                return;
            }

            Swal.fire({
                title: '¿Eliminar tarea?',
                text: `La tarea "${nombreTarea}" se eliminará. Seguro quiere continuar?.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    formulario.submit();
                }
            });
        });
    });

    const proyectoPagoInput = document.querySelector('#proyecto_id');
    const montoTotalInput = document.querySelector('#monto_total');
    const saldoPendienteActualInput = document.querySelector('#saldo_pendiente_actual');
    const montoPagadoInput = document.querySelector('#monto_pagado');

    const resumenMontoTotal = document.querySelector('#resumenMontoTotal');
    const resumenSaldoActual = document.querySelector('#resumenSaldoActual');
    const resumenMontoPagado = document.querySelector('#resumenMontoPagado');
    const resumenSaldo = document.querySelector('#resumenSaldo');

    const formatearDinero = (valor) => {
        const numero = Number(valor) || 0;

        return new Intl.NumberFormat('es-EC', {
            style: 'currency',
            currency: 'USD'
        }).format(numero);
    };

    const obtenerDatosProyectoPago = () => {
        if (!proyectoPagoInput) {
            return {
                montoTotal: 0,
                saldoPendiente: 0
            };
        }

        if (proyectoPagoInput.tagName === 'SELECT') {
            const opcion = proyectoPagoInput.options[proyectoPagoInput.selectedIndex];

            return {
                montoTotal: Number(opcion?.dataset.montoTotal) || 0,
                saldoPendiente: Number(opcion?.dataset.saldoPendiente) || 0
            };
        }

        return {
            montoTotal: Number(proyectoPagoInput.dataset.montoTotal) || 0,
            saldoPendiente: Number(proyectoPagoInput.dataset.saldoPendiente) || 0
        };
    };

    const actualizarResumenPago = () => {
        if (!montoTotalInput || !saldoPendienteActualInput || !montoPagadoInput) {
            return;
        }

        const datosProyecto = obtenerDatosProyectoPago();

        montoTotalInput.value = datosProyecto.montoTotal.toFixed(2);
        saldoPendienteActualInput.value = datosProyecto.saldoPendiente.toFixed(2);

        const montoPagado = Number(montoPagadoInput.value) || 0;
        const saldoDespues = Math.max(datosProyecto.saldoPendiente - montoPagado, 0);

        if (resumenMontoTotal) {
            resumenMontoTotal.textContent = formatearDinero(datosProyecto.montoTotal);
        }

        if (resumenSaldoActual) {
            resumenSaldoActual.textContent = formatearDinero(datosProyecto.saldoPendiente);
        }

        if (resumenMontoPagado) {
            resumenMontoPagado.textContent = formatearDinero(montoPagado);
        }

        if (resumenSaldo) {
            resumenSaldo.textContent = formatearDinero(saldoDespues);
        }
    };

    if (proyectoPagoInput && montoTotalInput && saldoPendienteActualInput && montoPagadoInput) {
        proyectoPagoInput.addEventListener('change', actualizarResumenPago);
        montoPagadoInput.addEventListener('input', actualizarResumenPago);
        actualizarResumenPago();
    }

    const formPago = document.querySelector('#form-pago');
    const metodoPagoSelect = document.querySelector('#metodo_pago');
    const comprobantesInput = document.querySelector('#comprobantes');

    if (formPago && metodoPagoSelect && comprobantesInput) {
        formPago.addEventListener('submit', (e) => {
            const metodoPago = metodoPagoSelect.value;
            const cantidadArchivos = comprobantesInput.files.length;

            if (metodoPago && metodoPago !== 'Efectivo' && cantidadArchivos === 0) {
                e.preventDefault();

                Swal.fire({
                    title: 'Comprobante requerido',
                    text: 'Para pagos que no sean en efectivo debes adjuntar al menos un comprobante.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#0057ff'
                });

                return;
            }

            if (cantidadArchivos > 2) {
                e.preventDefault();

                Swal.fire({
                    title: 'Máximo 2 comprobantes',
                    text: 'Solo puedes adjuntar hasta 2 archivos como comprobante de pago.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#0057ff'
                });
            }
        });
    }

    const formActualizarPago = document.querySelector('#form-actualizar-pago');
    const btnActualizarPago = document.querySelector('#btnActualizarPago');

    if (formActualizarPago && btnActualizarPago) {
        btnActualizarPago.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const resultado = await Swal.fire({
                title: 'Confirmar modificación',
                text: 'Ingresa tu contraseña para actualizar este pago.',
                input: 'password',
                inputPlaceholder: 'Contraseña',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirmar actualización',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0057ff',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debes ingresar tu contraseña';
                    }

                    return null;
                }
            });

            if (resultado.isConfirmed) {
                const passwordAnterior = formActualizarPago.querySelector('input[name="password_confirmacion"]');

                if (passwordAnterior) {
                    passwordAnterior.remove();
                }

                const inputPassword = document.createElement('input');
                inputPassword.type = 'hidden';
                inputPassword.name = 'password_confirmacion';
                inputPassword.value = resultado.value;

                formActualizarPago.appendChild(inputPassword);

                HTMLFormElement.prototype.submit.call(formActualizarPago);
            }
        });
    }

    const formActualizarNota = document.querySelector('#form-actualizar-nota');
    const btnActualizarNota = document.querySelector('#btnActualizarNota');

    if (formActualizarNota && btnActualizarNota) {
        btnActualizarNota.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const resultado = await Swal.fire({
                title: 'Confirmar modificación',
                text: 'Ingresa tu contraseña para actualizar esta nota.',
                input: 'password',
                inputPlaceholder: 'Contraseña',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirmar actualización',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0057ff',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debes ingresar tu contraseña';
                    }

                    return null;
                }
            });

            if (resultado.isConfirmed) {
                const passwordAnterior = formActualizarNota.querySelector('input[name="password_confirmacion"]');

                if (passwordAnterior) {
                    passwordAnterior.remove();
                }

                const inputPassword = document.createElement('input');
                inputPassword.type = 'hidden';
                inputPassword.name = 'password_confirmacion';
                inputPassword.value = resultado.value;

                formActualizarNota.appendChild(inputPassword);

                HTMLFormElement.prototype.submit.call(formActualizarNota);
            }
        });
    }

    const formEliminarNota = document.querySelector('#form-eliminar-nota');
    const btnEliminarNota = document.querySelector('#btnEliminarNota');

    if (formEliminarNota && btnEliminarNota) {
        btnEliminarNota.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const resultado = await Swal.fire({
                title: 'Eliminar nota',
                text: 'Esta acción ocultará la nota del sistema. Ingresa tu contraseña para confirmar.',
                input: 'password',
                inputPlaceholder: 'Contraseña',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar nota',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debes ingresar tu contraseña';
                    }

                    return null;
                }
            });

            if (resultado.isConfirmed) {
                const passwordAnterior = formEliminarNota.querySelector('input[name="password_confirmacion"]');

                if (passwordAnterior) {
                    passwordAnterior.remove();
                }

                const inputPassword = document.createElement('input');
                inputPassword.type = 'hidden';
                inputPassword.name = 'password_confirmacion';
                inputPassword.value = resultado.value;

                formEliminarNota.appendChild(inputPassword);

                HTMLFormElement.prototype.submit.call(formEliminarNota);
            }
        });
    }

    const selectoresPaginacion = document.querySelectorAll('select[name="per_page"]');

    selectoresPaginacion.forEach((selector) => {
        selector.addEventListener('change', () => {
            const formId = selector.getAttribute('form');
            const formulario = document.querySelector(`#${formId}`);

            if (!formulario) {
                return;
            }

            const pageInput = formulario.querySelector('input[name="page"]');

            if (pageInput) {
                pageInput.value = 1;
            }

            formulario.submit();
        });
    });

    const formFiltrosClientes = document.querySelector('#form-filtros-clientes');
    const inputBusquedaClientes = document.querySelector('#busqueda-clientes');

    if (formFiltrosClientes) {
        const selectEstadoClientes = formFiltrosClientes.querySelector('select[name="estado"]');
        const selectTipoClientes = formFiltrosClientes.querySelector('select[name="tipo_cliente"]');
        const pageInputClientes = formFiltrosClientes.querySelector('input[name="page"]');

        let timerBusquedaClientes;

        const enviarFiltrosClientes = () => {
            if (pageInputClientes) {
                pageInputClientes.value = 1;
            }

            formFiltrosClientes.submit();
        };

        if (inputBusquedaClientes) {
            inputBusquedaClientes.addEventListener('input', () => {
                clearTimeout(timerBusquedaClientes);

                const texto = inputBusquedaClientes.value.trim();

                timerBusquedaClientes = setTimeout(() => {
                    if (texto.length >= 3 || texto.length === 0) {
                        enviarFiltrosClientes();
                    }
                }, 500);
            });
        }

        if (selectEstadoClientes) {
            selectEstadoClientes.addEventListener('change', () => {
                enviarFiltrosClientes();
            });
        }

        if (selectTipoClientes) {
            selectTipoClientes.addEventListener('change', () => {
                enviarFiltrosClientes();
            });
        }
    }

    const formCrearCliente = document.querySelector('#form-crear-cliente');

    if (formCrearCliente) {
        const inputIdentificacion = formCrearCliente.querySelector('input[name="identificacion"]');

        formCrearCliente.addEventListener('submit', async (e) => {
            const yaActualizando = formCrearCliente.querySelector('input[name="actualizar_identificacion"]');

            if (yaActualizando) {
                return;
            }

            if (!inputIdentificacion) {
                return;
            }

            const identificacion = inputIdentificacion.value.trim();

            if (!identificacion) {
                return;
            }

            e.preventDefault();

            const datos = new FormData();
            datos.append('identificacion', identificacion);

            try {
                const respuesta = await fetch('/clientes/verificar-identificacion', {
                    method: 'POST',
                    body: datos
                });

                const resultado = await respuesta.json();

                if (!resultado.ok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resultado.mensaje || 'No se pudo verificar la identificación.'
                    });

                    return;
                }

                if (!resultado.existe) {
                    HTMLFormElement.prototype.submit.call(formCrearCliente);
                    return;
                }

                if (resultado.exacta) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cliente ya registrado',
                        html: `
                        <p>El cliente <strong>${resultado.cliente_nombre}</strong> ya está registrado con esta identificación.</p>
                        <p><strong>${resultado.identificacion_registrada}</strong></p>
                    `,
                        showCancelButton: true,
                        confirmButtonText: 'Ver cliente',
                        cancelButtonText: 'Cerrar',
                        confirmButtonColor: '#0057ff'
                    }).then((respuesta) => {
                        if (respuesta.isConfirmed) {
                            window.location.href = `/clientes/detalle?id=${resultado.cliente_id}`;
                        }
                    });

                    return;
                }

                if (resultado.puede_actualizar) {
                    const accion = resultado.tipo_ingresada === 'RUC'
                        ? 'actualizarlo a RUC'
                        : 'actualizarlo a cédula';

                    Swal.fire({
                        icon: 'question',
                        title: 'Cliente ya registrado',
                        html: `
                        <p>El cliente <strong>${resultado.cliente_nombre}</strong> ya está registrado con <strong>${resultado.tipo_registrada}</strong>.</p>
                        <p>Identificación actual: <strong>${resultado.identificacion_registrada}</strong></p>
                        <p>Nueva identificación ingresada: <strong>${resultado.identificacion_ingresada}</strong></p>
                        <p>¿Deseas ${accion}?</p>
                    `,
                        showCancelButton: true,
                        confirmButtonText: 'Sí, actualizar',
                        cancelButtonText: 'No, cancelar',
                        confirmButtonColor: '#0057ff',
                        cancelButtonColor: '#64748b'
                    }).then((respuesta) => {
                        if (!respuesta.isConfirmed) {
                            return;
                        }

                        const inputActualizar = document.createElement('input');
                        inputActualizar.type = 'hidden';
                        inputActualizar.name = 'actualizar_identificacion';
                        inputActualizar.value = '1';

                        const inputClienteId = document.createElement('input');
                        inputClienteId.type = 'hidden';
                        inputClienteId.name = 'cliente_existente_id';
                        inputClienteId.value = resultado.cliente_id;

                        const inputNuevaIdentificacion = document.createElement('input');
                        inputNuevaIdentificacion.type = 'hidden';
                        inputNuevaIdentificacion.name = 'nueva_identificacion';
                        inputNuevaIdentificacion.value = resultado.identificacion_ingresada;

                        formCrearCliente.appendChild(inputActualizar);
                        formCrearCliente.appendChild(inputClienteId);
                        formCrearCliente.appendChild(inputNuevaIdentificacion);

                        HTMLFormElement.prototype.submit.call(formCrearCliente);
                    });

                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Cliente duplicado',
                    text: 'Ya existe un cliente registrado con una identificación relacionada.'
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo verificar la identificación del cliente.'
                });
            }
        });
    }

    const formFiltrosProyectos = document.querySelector('#form-filtros-proyectos');
    const inputBusquedaProyectos = document.querySelector('#busqueda-proyectos');

    if (formFiltrosProyectos) {
        const selectClienteProyectos = formFiltrosProyectos.querySelector('select[name="cliente_id"]');
        const selectEstadoProyectos = formFiltrosProyectos.querySelector('select[name="estado"]');
        const selectPrioridadProyectos = formFiltrosProyectos.querySelector('select[name="prioridad"]');
        const pageInputProyectos = formFiltrosProyectos.querySelector('input[name="page"]');

        let timerBusquedaProyectos;

        const enviarFiltrosProyectos = () => {
            if (pageInputProyectos) {
                pageInputProyectos.value = 1;
            }

            formFiltrosProyectos.submit();
        };

        if (inputBusquedaProyectos) {
            inputBusquedaProyectos.addEventListener('input', () => {
                clearTimeout(timerBusquedaProyectos);

                const texto = inputBusquedaProyectos.value.trim();

                timerBusquedaProyectos = setTimeout(() => {
                    if (texto.length >= 3 || texto.length === 0) {
                        enviarFiltrosProyectos();
                    }
                }, 500);
            });
        }

        if (selectClienteProyectos) {
            selectClienteProyectos.addEventListener('change', enviarFiltrosProyectos);
        }

        if (selectEstadoProyectos) {
            selectEstadoProyectos.addEventListener('change', enviarFiltrosProyectos);
        }

        if (selectPrioridadProyectos) {
            selectPrioridadProyectos.addEventListener('change', enviarFiltrosProyectos);
        }
    }

    const formFiltrosTareas = document.querySelector('#form-filtros-tareas');
    const inputBusquedaTareas = document.querySelector('#busqueda-tareas');

    if (formFiltrosTareas) {
        const selectProyectoTareas = formFiltrosTareas.querySelector('select[name="proyecto_id"]');
        const selectEstadoTareas = formFiltrosTareas.querySelector('select[name="estado"]');
        const selectPrioridadTareas = formFiltrosTareas.querySelector('select[name="prioridad"]');
        const pageInputTareas = formFiltrosTareas.querySelector('input[name="page"]');

        let timerBusquedaTareas;

        const enviarFiltrosTareas = () => {
            if (pageInputTareas) {
                pageInputTareas.value = 1;
            }

            formFiltrosTareas.submit();
        };

        if (inputBusquedaTareas) {
            inputBusquedaTareas.addEventListener('input', () => {
                clearTimeout(timerBusquedaTareas);

                const texto = inputBusquedaTareas.value.trim();

                timerBusquedaTareas = setTimeout(() => {
                    if (texto.length >= 3 || texto.length === 0) {
                        enviarFiltrosTareas();
                    }
                }, 500);
            });
        }

        if (selectProyectoTareas) {
            selectProyectoTareas.addEventListener('change', enviarFiltrosTareas);
        }

        if (selectEstadoTareas) {
            selectEstadoTareas.addEventListener('change', enviarFiltrosTareas);
        }

        if (selectPrioridadTareas) {
            selectPrioridadTareas.addEventListener('change', enviarFiltrosTareas);
        }
    }

    const formFiltrosPagos = document.querySelector('#form-filtros-pagos');
    const inputBusquedaPagos = document.querySelector('#busqueda-pagos');

    if (formFiltrosPagos) {
        const selectProyectoPagos = formFiltrosPagos.querySelector('select[name="proyecto_id"]');
        const selectClientePagos = formFiltrosPagos.querySelector('select[name="cliente_id"]');
        const selectEstadoPagos = formFiltrosPagos.querySelector('select[name="estado"]');
        const selectMetodoPagos = formFiltrosPagos.querySelector('select[name="metodo_pago"]');
        const pageInputPagos = formFiltrosPagos.querySelector('input[name="page"]');

        let timerBusquedaPagos;

        const enviarFiltrosPagos = () => {
            if (pageInputPagos) {
                pageInputPagos.value = 1;
            }

            formFiltrosPagos.submit();
        };

        if (inputBusquedaPagos) {
            inputBusquedaPagos.addEventListener('input', () => {
                clearTimeout(timerBusquedaPagos);

                const texto = inputBusquedaPagos.value.trim();

                timerBusquedaPagos = setTimeout(() => {
                    if (texto.length >= 3 || texto.length === 0) {
                        enviarFiltrosPagos();
                    }
                }, 500);
            });
        }

        if (selectProyectoPagos) {
            selectProyectoPagos.addEventListener('change', enviarFiltrosPagos);
        }

        if (selectEstadoPagos) {
            selectEstadoPagos.addEventListener('change', enviarFiltrosPagos);
        }

        if (selectMetodoPagos) {
            selectMetodoPagos.addEventListener('change', enviarFiltrosPagos);
        }

        if (selectClientePagos) {
            selectClientePagos.addEventListener('change', enviarFiltrosPagos);
        }
    }

    const formFiltrosNotas = document.querySelector('#form-filtros-notas');
    const inputBusquedaNotas = document.querySelector('#busqueda-notas');

    if (formFiltrosNotas) {
        const selectClienteNotas = formFiltrosNotas.querySelector('select[name="cliente_id"]');
        const selectProyectoNotas = formFiltrosNotas.querySelector('select[name="proyecto_id"]');
        const selectColorNotas = formFiltrosNotas.querySelector('select[name="color"]');
        const pageInputNotas = formFiltrosNotas.querySelector('input[name="page"]');

        let timerBusquedaNotas;

        const enviarFiltrosNotas = () => {
            if (pageInputNotas) {
                pageInputNotas.value = 1;
            }

            formFiltrosNotas.submit();
        };

        if (inputBusquedaNotas) {
            inputBusquedaNotas.addEventListener('input', () => {
                clearTimeout(timerBusquedaNotas);

                const texto = inputBusquedaNotas.value.trim();

                timerBusquedaNotas = setTimeout(() => {
                    if (texto.length >= 3 || texto.length === 0) {
                        enviarFiltrosNotas();
                    }
                }, 500);
            });
        }

        if (selectClienteNotas) {
            selectClienteNotas.addEventListener('change', enviarFiltrosNotas);
        }

        if (selectProyectoNotas) {
            selectProyectoNotas.addEventListener('change', enviarFiltrosNotas);
        }

        if (selectColorNotas) {
            selectColorNotas.addEventListener('change', enviarFiltrosNotas);
        }
    }

    const botonesDesbloquearNotas = document.querySelectorAll('.js-desbloquear-notas');

    botonesDesbloquearNotas.forEach((boton) => {
        boton.addEventListener('click', async () => {
            const redireccion = boton.dataset.redireccion || window.location.href;
            const proyectoId = boton.dataset.proyectoId || '';
            const clienteId = boton.dataset.clienteId || '';

            const resultado = await Swal.fire({
                icon: 'question',
                title: 'Desbloquear notas',
                text: 'Ingresa tu contraseña para visualizar las notas protegidas.',
                input: 'password',
                inputPlaceholder: 'Contraseña',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocomplete: 'current-password'
                },
                showCancelButton: true,
                confirmButtonText: 'Desbloquear',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0057ff',
                cancelButtonColor: '#64748b',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Ingresa tu contraseña';
                    }

                    return null;
                }
            });

            if (!resultado.isConfirmed) {
                return;
            }

            const datos = new FormData();
            datos.append('password', resultado.value);

            if (proyectoId) {
                datos.append('proyecto_id', proyectoId);
            }

            if (clienteId) {
                datos.append('cliente_id', clienteId);
            }

            try {
                const respuesta = await fetch('/notas/desbloquear-ajax', {
                    method: 'POST',
                    body: datos
                });

                const data = await respuesta.json();

                if (!data.ok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudo desbloquear',
                        text: data.mensaje || 'La contraseña ingresada no es correcta.',
                        confirmButtonColor: '#0057ff'
                    });

                    return;
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Notas desbloqueadas',
                    text: 'Ahora puedes visualizar las notas protegidas.',
                    timer: 1200,
                    showConfirmButton: false
                });

                window.location.href = redireccion;
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo verificar la contraseña.',
                    confirmButtonColor: '#0057ff'
                });
            }
        });
    });

    const bloquearNotasFueraDeModulo = () => {
        const rutaActual = window.location.pathname;
        const parametros = new URLSearchParams(window.location.search);

        const estaEnModuloNotas = rutaActual === '/notas';
        const estaEnDesbloquearNotas = rutaActual === '/notas/desbloquear';

        const estaEnDetalleProyecto = rutaActual === '/proyectos/detalle' && parametros.has('id');
        const estaEnDetalleCliente = rutaActual === '/clientes/detalle' && parametros.has('id');

        if (
            estaEnModuloNotas ||
            estaEnDesbloquearNotas ||
            estaEnDetalleProyecto ||
            estaEnDetalleCliente
        ) {
            return;
        }

        fetch('/notas/bloquear-ajax', {
            method: 'POST'
        }).catch(() => { });
    };

    bloquearNotasFueraDeModulo();

    const inputAvatar = document.querySelector('#avatar');
    const btnSeleccionarAvatar = document.querySelector('#btn-seleccionar-avatar');
    const avatarModal = document.querySelector('#avatar-modal');
    const avatarCropImg = document.querySelector('#avatar-crop-img');
    const avatarCropArea = document.querySelector('#avatar-crop-area');
    const avatarZoom = document.querySelector('#avatar-zoom');
    const btnGuardarRecorte = document.querySelector('#btn-guardar-recorte');
    const inputAvatarBase64 = document.querySelector('#avatar_base64');
    const avatarPreview = document.querySelector('#avatar-preview');
    const botonesCerrarAvatar = document.querySelectorAll('[data-cerrar-avatar]');

    if (
        inputAvatar &&
        btnSeleccionarAvatar &&
        avatarModal &&
        avatarCropImg &&
        avatarCropArea &&
        avatarZoom &&
        btnGuardarRecorte &&
        inputAvatarBase64 &&
        avatarPreview
    ) {
        let imagenOriginal = null;
        let zoom = 1;
        let offsetX = 0;
        let offsetY = 0;
        let arrastrando = false;
        let inicioX = 0;
        let inicioY = 0;

        const abrirModalAvatar = () => {
            avatarModal.classList.add('avatar-modal--activo');
            avatarModal.setAttribute('aria-hidden', 'false');
        };

        const cerrarModalAvatar = () => {
            avatarModal.classList.remove('avatar-modal--activo');
            avatarModal.setAttribute('aria-hidden', 'true');
        };

        const actualizarTransformAvatar = () => {
            avatarCropImg.style.transform = `translate(${offsetX}px, ${offsetY}px) scale(${zoom})`;
        };

        btnSeleccionarAvatar.addEventListener('click', () => {
            inputAvatar.click();
        });

        inputAvatar.addEventListener('change', () => {
            const archivo = inputAvatar.files[0];

            if (!archivo) {
                return;
            }

            const tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];

            if (!tiposPermitidos.includes(archivo.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Formato no válido',
                    text: 'El avatar debe ser una imagen JPG, PNG o WEBP.',
                    confirmButtonColor: '#0057ff'
                });

                inputAvatar.value = '';
                return;
            }

            if (archivo.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Imagen muy pesada',
                    text: 'El avatar no debe superar los 2MB.',
                    confirmButtonColor: '#0057ff'
                });

                inputAvatar.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = (evento) => {
                imagenOriginal = new Image();

                imagenOriginal.onload = () => {
                    avatarCropImg.src = evento.target.result;

                    zoom = 1;
                    offsetX = 0;
                    offsetY = 0;
                    avatarZoom.value = 1;

                    actualizarTransformAvatar();
                    abrirModalAvatar();
                };

                imagenOriginal.src = evento.target.result;
            };

            reader.readAsDataURL(archivo);
        });

        avatarZoom.addEventListener('input', () => {
            zoom = parseFloat(avatarZoom.value);
            actualizarTransformAvatar();
        });

        avatarCropArea.addEventListener('mousedown', (e) => {
            arrastrando = true;
            inicioX = e.clientX - offsetX;
            inicioY = e.clientY - offsetY;
            avatarCropArea.classList.add('avatar-crop__area--drag');
        });

        window.addEventListener('mousemove', (e) => {
            if (!arrastrando) {
                return;
            }

            offsetX = e.clientX - inicioX;
            offsetY = e.clientY - inicioY;

            actualizarTransformAvatar();
        });

        window.addEventListener('mouseup', () => {
            arrastrando = false;
            avatarCropArea.classList.remove('avatar-crop__area--drag');
        });

        avatarCropArea.addEventListener('touchstart', (e) => {
            if (!e.touches[0]) {
                return;
            }

            arrastrando = true;
            inicioX = e.touches[0].clientX - offsetX;
            inicioY = e.touches[0].clientY - offsetY;
        });

        avatarCropArea.addEventListener('touchmove', (e) => {
            if (!arrastrando || !e.touches[0]) {
                return;
            }

            offsetX = e.touches[0].clientX - inicioX;
            offsetY = e.touches[0].clientY - inicioY;

            actualizarTransformAvatar();
        });

        avatarCropArea.addEventListener('touchend', () => {
            arrastrando = false;
        });

        btnGuardarRecorte.addEventListener('click', () => {
            if (!imagenOriginal) {
                return;
            }

            const cropSize = avatarCropArea.offsetWidth;
            const canvasSize = 500;

            const canvas = document.createElement('canvas');
            canvas.width = canvasSize;
            canvas.height = canvasSize;

            const ctx = canvas.getContext('2d');

            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvasSize, canvasSize);

            const baseScale = Math.max(
                cropSize / imagenOriginal.naturalWidth,
                cropSize / imagenOriginal.naturalHeight
            );

            const displayedWidth = imagenOriginal.naturalWidth * baseScale * zoom;
            const displayedHeight = imagenOriginal.naturalHeight * baseScale * zoom;

            const left = (cropSize - displayedWidth) / 2 + offsetX;
            const top = (cropSize - displayedHeight) / 2 + offsetY;

            const factor = canvasSize / cropSize;

            ctx.drawImage(
                imagenOriginal,
                left * factor,
                top * factor,
                displayedWidth * factor,
                displayedHeight * factor
            );

            const imagenRecortada = canvas.toDataURL('image/jpeg', 0.9);

            inputAvatarBase64.value = imagenRecortada;

            avatarPreview.innerHTML = `<img src="${imagenRecortada}" alt="Avatar seleccionado">`;

            cerrarModalAvatar();

            Swal.fire({
                icon: 'success',
                title: 'Foto lista',
                text: 'La imagen fue recortada. Presiona Guardar cambios para actualizar tu perfil.',
                timer: 1700,
                showConfirmButton: false
            });
        });

        botonesCerrarAvatar.forEach((boton) => {
            boton.addEventListener('click', cerrarModalAvatar);
        });
    }

    const scrollPerfilHash = () => {
        const hash = window.location.hash;

        if (!hash) {
            return;
        }

        const elemento = document.querySelector(hash);

        if (!elemento) {
            return;
        }

        setTimeout(() => {
            const offsetTop = elemento.getBoundingClientRect().top + window.scrollY - 110;

            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }, 250);
    };

    scrollPerfilHash();

    const formFiltrosUsuarios = document.querySelector('#form-filtros-usuarios');
    const inputBusquedaUsuarios = document.querySelector('#busqueda-usuarios');

    if (formFiltrosUsuarios) {
        const selectEstadoUsuarios = formFiltrosUsuarios.querySelector('select[name="estado"]');
        const selectConfirmadoUsuarios = formFiltrosUsuarios.querySelector('select[name="confirmado"]');
        const selectRolUsuarios = formFiltrosUsuarios.querySelector('select[name="rol_id"]');

        let timerBusquedaUsuarios;

        const enviarFiltrosUsuarios = () => {
            formFiltrosUsuarios.submit();
        };

        if (inputBusquedaUsuarios) {
            inputBusquedaUsuarios.addEventListener('input', () => {
                clearTimeout(timerBusquedaUsuarios);

                const texto = inputBusquedaUsuarios.value.trim();

                timerBusquedaUsuarios = setTimeout(() => {
                    if (texto.length >= 3 || texto.length === 0) {
                        enviarFiltrosUsuarios();
                    }
                }, 500);
            });
        }

        if (selectEstadoUsuarios) selectEstadoUsuarios.addEventListener('change', enviarFiltrosUsuarios);
        if (selectConfirmadoUsuarios) selectConfirmadoUsuarios.addEventListener('change', enviarFiltrosUsuarios);
        if (selectRolUsuarios) selectRolUsuarios.addEventListener('change', enviarFiltrosUsuarios);
    }

    const formulariosUsuarios = document.querySelectorAll('.usuarios-acciones');

    formulariosUsuarios.forEach((formulario) => {
        formulario.addEventListener('submit', async (e) => {
            e.preventDefault();

            const resultado = await Swal.fire({
                icon: 'question',
                title: 'Actualizar usuario',
                text: '¿Seguro que deseas actualizar el rol o estado de este usuario?',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0057ff',
                cancelButtonColor: '#64748b'
            });

            if (resultado.isConfirmed) {
                formulario.submit();
            }
        });
    });

    const selectFormatoFecha = document.querySelector('#formato_fecha');
    const previewFormatoFecha = document.querySelector('#preview-formato-fecha');

    if (selectFormatoFecha && previewFormatoFecha) {
        const formatosFecha = {
            dd_mm_yyyy: '03/08/2026',
            dd_mes_yyyy: '03 Agosto 2026',
            dia_dd_mes_yyyy: 'Lunes, 03 de Agosto del 2026',
            dd_mm_yy: '03/08/26',
            mes_dd_yyyy: 'Agosto, 03 del 2026'
        };

        selectFormatoFecha.addEventListener('change', () => {
            previewFormatoFecha.textContent = formatosFecha[selectFormatoFecha.value] || '03/08/2026';
        });
    }
});