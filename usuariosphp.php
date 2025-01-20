<?php
require 'csrfPROTECAO.php';
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require_once "../../configdb.php";

class MostrandoUsuarios
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function usuarios($filtro = "", $offset = 0, $limite = 50)
    {
        $query = "SELECT u.id, u.nome, u.email, u.telefone, u.tipo_usuario, 
                        TO_CHAR(u.data_nascimento, 'DD/MM/YYYY') AS data_nascimento, g.sexo
                FROM usuarios AS u
                INNER JOIN genero g ON g.id = u.id_sexo";

        if (!empty($filtro)) {
            $query .= " WHERE 
                        u.nome ILIKE :filtro OR 
                        u.email ILIKE :filtro OR 
                        u.telefone ILIKE :filtro OR 
                        u.tipo_usuario ILIKE :filtro OR 
                        TO_CHAR(u.data_nascimento, 'DD/MM/YYYY') ILIKE :filtro OR 
                        g.sexo ILIKE :filtro";
        }

        $query .= " ORDER BY u.id ASC LIMIT :limite OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if (!empty($filtro)) {
            $stmt->bindValue(':filtro', '%' . $filtro . '%');
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        echo "<table>";
        echo "<thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Tipo</th>
                    <th>Data de Nascimento</th>
                    <th>Sexo</th>
                </tr>
            </thead>";
        echo "<tbody>";

        while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($linha['id']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['email']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['telefone']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['tipo_usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['data_nascimento']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['sexo']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    }
}
