import 'bootstrap';
import 'sweetalert2/dist/sweetalert2.min.css';
import * as Turbo from '@hotwired/turbo';
import './stimulus_bootstrap.js';

// Deixa a barra de progresso do Turbo aparecer quase na hora do clique, em
// vez de só depois de 500ms (padrão) — dá feedback visual mesmo quando a
// navegação é rápida (paginação, links do menu).
Turbo.config.drive.progressBarDelay = 100;
