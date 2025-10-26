document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('empresaForm');
    const btnCadastrar = document.getElementById('btnCadastrar');
    const btnCancelar = document.getElementById('btnCancelar');
    const messageDiv = document.getElementById('message');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar campos
        const nomeEmpresa = document.getElementById('nome_empresa').value.trim();
        const atuacao = document.getElementById('atuacao').value.trim();
        const email = document.getElementById('email').value.trim();
        
        if(!nomeEmpresa || !atuacao || !email) {
            showMessage('Por favor, preencha todos os campos.', 'error');
            return;
        }
        
        if(!isValidEmail(email)) {
            showMessage('Por favor, insira um e-mail válido.', 'error');
            return;
        }
        
        const formData = new FormData(this);
        
        showMessage('Processando...', 'success');
        
        fetch('processar.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showMessage(data.message, data.success ? 'success' : 'error');
            
            if(data.success) {
                form.reset();
                document.getElementById('empresaId').value = '';
                btnCadastrar.textContent = '💾 Cadastrar';
                btnCancelar.classList.add('hidden');
                // Recarregar a página após 1 segundo
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        })
        .catch(error => {
            showMessage('Erro ao processar solicitação. Tente novamente.', 'error');
            console.error('Error:', error);
        });
    });

    btnCancelar.addEventListener('click', function() {
        form.reset();
        document.getElementById('empresaId').value = '';
        btnCadastrar.textContent = '💾 Cadastrar';
        this.classList.add('hidden');
        showMessage('Edição cancelada.', 'success');
    });
});

function showMessage(message, type) {
    const messageDiv = document.getElementById('message');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    messageDiv.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
    
    setTimeout(() => {
        if(messageDiv.innerHTML.includes(message)) {
            messageDiv.innerHTML = '';
        }
    }, 5000);
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function enviarEmail(email) {
    // Abre o gerenciador de e-mail padrão do usuário
    window.location.href = `mailto:${email}?subject=Contato&body=Prezados,%0D%0A%0D%0AGostaríamos de entrar em contato...`;
}

function editarEmpresa(id) {
    showMessage('Carregando dados da empresa...', 'success');
    
    fetch(`buscar_empresa.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('empresaId').value = data.empresa.id;
                document.getElementById('nome_empresa').value = data.empresa.nome_empresa;
                document.getElementById('atuacao').value = data.empresa.atuacao;
                document.getElementById('email').value = data.empresa.email;
                
                document.getElementById('btnCadastrar').textContent = '💾 Atualizar';
                document.getElementById('btnCancelar').classList.remove('hidden');
                
                // Scroll para o formulário
                document.querySelector('.form-container').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'center'
                });
                
                showMessage('Empresa carregada para edição.', 'success');
            } else {
                showMessage('Erro ao carregar dados da empresa.', 'error');
            }
        })
        .catch(error => {
            showMessage('Erro ao carregar dados da empresa.', 'error');
            console.error('Error:', error);
        });
}

function excluirEmpresa(id) {
    const empresaNome = document.querySelector(`#empresa-${id} td:first-child`).textContent;
    
    if(confirm(`Tem certeza que deseja excluir a empresa "${empresaNome}"?`)) {
        showMessage('Excluindo empresa...', 'success');
        
        fetch(`excluir_empresa.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                showMessage(data.message, data.success ? 'success' : 'error');
                
                if(data.success) {
                    // Remover a linha da tabela com animação
                    const linha = document.getElementById(`empresa-${id}`);
                    if(linha) {
                        linha.style.backgroundColor = '#ffebee';
                        linha.style.transition = 'all 0.5s';
                        setTimeout(() => {
                            linha.remove();
                            // Se não há mais linhas, recarrega a página
                            const linhasRestantes = document.querySelectorAll('#tabelaEmpresas tbody tr');
                            if(linhasRestantes.length === 1 && linhasRestantes[0].querySelector('.empty-state')) {
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        }, 500);
                    }
                }
            })
            .catch(error => {
                showMessage('Erro ao excluir empresa.', 'error');
                console.error('Error:', error);
            });
    }
}