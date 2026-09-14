import { Controller } from '@hotwired/stimulus';

// Mostra um spinner dentro do próprio link no instante do clique.
// Existe porque data-turbo-submits-with (usado nos botões de formulário) só
// funciona pra submits, não pra navegação por <a>.
export default class extends Controller {
    static values = { text: { type: String, default: 'Carregando...' } };

    click() {
        this.element.classList.add('disabled');
        this.element.setAttribute('aria-disabled', 'true');
        this.element.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${this.textValue}`;
    }
}
