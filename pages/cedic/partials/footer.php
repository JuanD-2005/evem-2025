<?php
/* ══════════════════════════════════════════════════════════════════
   partials/footer.php — footer + carga del JS + cierre del documento

   Reutiliza cedic_href() de nav.php, que ya está en memoria porque
   head.php incluye nav.php antes del contenido.
   El año del copyright se calcula solo.
   ══════════════════════════════════════════════════════════════════ */

$a = fn(string $id): string => cedic_ancla($id, $pagina, $LANDING);

/* Estos tres respetan el interruptor $PAGINAS_SEPARADAS de config.php */
$h_act = cedic_href(['label'=>''] + $DESTINOS['actividades'], $pagina, $LANDING);
$h_for = cedic_href(['label'=>''] + $DESTINOS['formacion'],   $pagina, $LANDING);
$h_con = cedic_href(['label'=>''] + $DESTINOS['contenidos'],  $pagina, $LANDING);
?>
    <!-- ═══════════════════ FOOTER ═══════════════════ -->
    <footer class="footer">
        <div class="wrap">
            <div class="footer-grid">

                <div class="footer-col footer-col-identity">
                    <h4>Universidad Nacional Experimental del Táchira</h4>
                    <p class="footer-lead">Centro de Enseñanza y Divulgación de las Ciencias.</p>
                    <p>Av. Universidad, Paramillo</p>
                    <p>San Cristóbal, Estado Táchira</p>
                    <p>Venezuela</p>
                    <p class="footer-phone"><svg class="ico" aria-hidden="true"><use href="#i-phone"/></svg> (0414) 707 7573</p>
                    <p class="footer-email"><svg class="ico" aria-hidden="true"><use href="#i-mail"/></svg> matyfis@unet.edu.ve</p>
                    <p class="footer-email"><svg class="ico" aria-hidden="true"><use href="#i-mail"/></svg> gilbpar@gmail.com</p>
                </div>

                <div class="footer-col">
                    <h4>Portal</h4>
                    <ul class="footer-links">
                        <li><a href="../../index.html"><svg class="ico" aria-hidden="true"><use href="#i-home"/></svg> Inicio EVEM</a></li>
                        <li><a href="../festival/festival.html"><svg class="ico" aria-hidden="true"><use href="#i-rocket"/></svg> Festival de Ciencias</a></li>
                        <li><a href="../dim/dim.html"><svg class="ico" aria-hidden="true"><use href="#i-circle-dot"/></svg> Evento DIM</a></li>
                        <li><a href="<?= $h_act ?>"><svg class="ico" aria-hidden="true"><use href="#i-sparkles"/></svg> Actividades</a></li>
                        <li><a href="<?= $h_for ?>"><svg class="ico" aria-hidden="true"><use href="#i-graduation"/></svg> Cursos y talleres</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>CEDIC</h4>
                    <ul class="footer-links">
                        <li><a href="<?= $a('nosotros') ?>">Quiénes somos</a></li>
                        <li><a href="<?= $a('equipo') ?>">Staff y docentes</a></li>
                        <li><a href="<?= $h_con ?>">Contenidos académicos</a></li>
                        <li><a href="<?= $a('contacto') ?>">Sede y contacto</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contacto</h4>
                    <p>Email: evem@unet.edu.ve</p>
                    <p>Edificio F, antigua Casita de AsoVAC</p>
                    <p>UNET — San Cristóbal</p>
                </div>

            </div>

            <div class="footer-bottom">
                <p>© <?= date('Y') ?> CEDIC · Universidad Nacional Experimental del Táchira. Todos los derechos reservados.</p>
                <div style="margin-top: 16px; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                    <p style="font-size: 0.82rem; color: rgba(255, 255, 255, 0.6); margin: 0; letter-spacing: 0.02em;">
                        Diseñado y desarrollado por
                        <span style="color: white; font-weight: 600">Juan Paredes</span>
                    </p>
                    <div style="display: flex; gap: 16px; align-items: center">
                        <a href="https://github.com/JuanD-2005" target="_blank" rel="noopener noreferrer" style="color: rgba(255, 255, 255, 0.6); transition: color 0.3s ease;" onmouseover="this.style.color = '#ffffff'" onmouseout="this.style.color = 'rgba(255,255,255,0.6)'" title="GitHub">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.646.35-1.086.636-1.336-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.161 22 16.416 22 12c0-5.523-4.477-10-10-10z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/in/juan-diego-paredes-g%C3%A1mez-21415338a/" target="_blank" rel="noopener noreferrer" style="color: rgba(255, 255, 255, 0.6); transition: color 0.3s ease;" onmouseover="this.style.color = '#ffffff'" onmouseout="this.style.color = 'rgba(255,255,255,0.6)'" title="LinkedIn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="../../js/cedic.js"></script>
    <script src="../../js/animations.js"></script>
</body>
</html>
