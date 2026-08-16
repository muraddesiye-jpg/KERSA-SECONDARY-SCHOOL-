<?php
require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        listNews();
        break;
    case 'get':
        getNews();
        break;
    case 'create':
        createNews();
        break;
    case 'update':
        updateNews();
        break;
    case 'delete':
        deleteNews();
        break;
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
}

function listNews() {
    $conn = getConnection();
    
    $sql = "SELECT * FROM news ORDER BY publish_date DESC";
    $result = $conn->query($sql);
    
    $news = [];
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'news' => $news
    ]);
    
    $conn->close();
}

function getNews() {
    $id = $_GET['id'] ?? 0;
    
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'news' => $row
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'News not found'
        ]);
    }
    
    $stmt->close();
    $conn->close();
}

function createNews() {
    requireLogin();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
        return;
    }
    
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $author = $_SESSION['full_name'];
    
    if (empty($title) || empty($content) || empty($category)) {
        echo json_encode([
            'success' => false,
            'message' => 'Title, content, and category are required'
        ]);
        return;
    }
    
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO news (title, content, category, author, publish_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $title, $content, $category, $author);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'News created successfully',
            'id' => $stmt->insert_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error creating news: ' . $conn->error
        ]);
    }
    
    $stmt->close();
    $conn->close();
}

function updateNews() {
    requireLogin();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
        return;
    }
    
    $id = $_POST['id'] ?? 0;
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE news SET title = ?, content = ?, category = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $content, $category, $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'News updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating news: ' . $conn->error
        ]);
    }
    
    $stmt->close();
    $conn->close();
}

function deleteNews() {
    requireLogin();
    
    $id = $_GET['id'] ?? 0;
    
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'News deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting news: ' . $conn->error
        ]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
