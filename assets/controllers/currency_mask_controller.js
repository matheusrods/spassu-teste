import { Controller } from '@hotwired/stimulus';

// Trata os dígitos digitados como centavos (ex: digitar 200 -> R$ 2,00;
// digitar 200000 -> R$ 2.000,00), como em apps de banco/pagamento no Brasil.
// Formata em pt-BR (milhar "." / decimal ","), compatível com o que o
// MoneyType do Symfony espera ao submeter (framework.default_locale=pt_BR).
export default class extends Controller {
    connect() {
        this.render(this.toCents(this.element.value));
    }

    input() {
        this.render(this.toCents(this.element.value));
    }

    toCents(raw) {
        const digits = raw.replace(/\D/g, '').replace(/^0+(?=\d)/, '');

        return digits === '' ? 0 : parseInt(digits, 10);
    }

    render(cents) {
        if (cents === 0) {
            this.element.value = '';

            return;
        }

        const [integerPart, decimalPart] = (cents / 100).toFixed(2).split('.');
        const grouped = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        this.element.value = `${grouped},${decimalPart}`;
        this.element.setSelectionRange(this.element.value.length, this.element.value.length);
    }
}
