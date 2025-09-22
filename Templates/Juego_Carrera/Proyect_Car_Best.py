import pygame
from pygame.locals import *
import random


pygame.init()


width, height = 500, 500
screen = pygame.display.set_mode((width, height))
pygame.display.set_caption('Feria de la Ciencia 2025')


gray = (100, 100, 100)
green = (76, 208, 56)
red = (200, 0, 0)
white = (255, 255, 255)
yellow = (255, 232, 0)
black = (0, 0, 0)


road_width = 300
marker_width = 10
marker_height = 50

left_lane = 150
center_lane = 250
right_lane = 350
lanes = [left_lane, center_lane, right_lane]

road = (100, 0, road_width, height)
left_edge_marker = (100, 0, marker_width, height)
right_edge_marker = (389, 0, marker_width, height)

player_x, player_y = 250, 400
lane_marker_move_y = 0
clock = pygame.time.Clock()
fps = 120


speed = 2
puntaje = 0
max_puntaje = 0
nombre_max = ""
move_left = False
move_right = False
gameover = False
noche = False

font_default = pygame.font.Font(pygame.font.get_default_font(), 20)


def pedir_nombre():
    nombre = ""
    input_box = pygame.Rect(width // 2 - 150, height // 2 - 25, 300, 50)
    color_inactive = pygame.Color('lightskyblue3')
    color_active = pygame.Color('dodgerblue2')
    color = color_inactive
    active = False

    while True:
        for event in pygame.event.get():
            if event.type == QUIT:
                pygame.quit()
                exit()
            if event.type == MOUSEBUTTONDOWN:
                active = input_box.collidepoint(event.pos)
                color = color_active if active else color_inactive
            if event.type == KEYDOWN and active:
                if event.key == K_RETURN and nombre.strip():
                    return nombre.strip()
                elif event.key == K_BACKSPACE:
                    nombre = nombre[:-1]
                elif len(nombre) < 20:
                    nombre += event.unicode

        screen.fill(black)
        prompt = font_default.render("Ingrese su nombre y presione ENTER:", True, white)
        screen.blit(prompt, (width // 2 - prompt.get_width() // 2, height // 2 - 100))
        txt_surface = font_default.render(nombre, True, color)
        input_box.w = max(300, txt_surface.get_width() + 10)
        pygame.draw.rect(screen, color, input_box, 2)
        screen.blit(txt_surface, (input_box.x + 5, input_box.y + 10))
        pygame.display.flip()
        clock.tick(30)

def mostrar_nombre(nombre, noche):
    font = pygame.font.Font(pygame.font.get_default_font(), 16)
    color = white if not noche else (220, 220, 220)
    name_text = font.render(f'Conductor: {nombre}', True, color)
    name_rect = name_text.get_rect(center=(width // 2, height - 20))
    screen.blit(name_text, name_rect)


class Vehicle(pygame.sprite.Sprite):
    def __init__(self, image, x, y, name=""):
        super().__init__()
        image_scale = 45 / image.get_rect().width
        size = (int(image.get_rect().width * image_scale), int(image.get_rect().height * image_scale))
        self.image = pygame.transform.scale(image, size)
        self.rect = self.image.get_rect(center=(x, y))
        self.name = name
        self.move_direction = random.choice([-1, 1])
        self.move_counter = 0

    def update(self):
        if self.name != 'img_3.png':
            self.rect.x += self.move_direction * 2
            self.move_counter += 1
            if self.move_counter > random.randint(60, 120):
                self.move_direction *= -1
                self.move_counter = 0
            if self.rect.left <= 101:
                self.rect.left = 101
                self.move_direction = 1
            elif self.rect.right >= 389:
                self.rect.right = 389
                self.move_direction = -1

class PlayerVehicle(Vehicle):
    def __init__(self, x, y):
        image = pygame.image.load('Source/img.png')
        super().__init__(image, x, y)


image_filenames = ['img_1.png', 'img_2.png', 'img_3.png', 'img_5.png']
vehicle_imagenes = [(f, pygame.image.load('Source/' + f)) for f in image_filenames]
crash = pygame.image.load('Source/img_4.png')
crash_rect = crash.get_rect()


nombre_conductor = pedir_nombre()


player_group = pygame.sprite.Group()
vehicle_group = pygame.sprite.Group()
player = PlayerVehicle(player_x, player_y)
player_group.add(player)


running = True
while running:
    clock.tick(fps)
    noche = puntaje > 10

    for event in pygame.event.get():
        if event.type == QUIT:
            running = False
        if event.type == KEYDOWN:
            if event.key == K_LEFT:
                move_left = True
            elif event.key == K_RIGHT:
                move_right = True
        if event.type == KEYUP:
            if event.key == K_LEFT:
                move_left = False
            elif event.key == K_RIGHT:
                move_right = False


    if noche:
        screen.fill(black)
        pygame.draw.rect(screen, (40, 40, 40), road)
        pygame.draw.rect(screen, (255, 215, 0), left_edge_marker)
        pygame.draw.rect(screen, (255, 215, 0), right_edge_marker)
    else:
        screen.fill(green)
        pygame.draw.rect(screen, gray, road)
        pygame.draw.rect(screen, yellow, left_edge_marker)
        pygame.draw.rect(screen, yellow, right_edge_marker)


    lane_marker_move_y += speed * 2
    if lane_marker_move_y >= marker_height * 2:
        lane_marker_move_y = 0
    for y in range(marker_height * -2, height, marker_height * 2):
        color_marker = white if not noche else (200, 200, 200)
        pygame.draw.rect(screen, color_marker, (left_lane + 45, y + lane_marker_move_y, marker_width, marker_height))
        pygame.draw.rect(screen, color_marker, (center_lane + 45, y + lane_marker_move_y, marker_width, marker_height))


    if move_left:
        player.rect.x -= 4
        if player.rect.left < 101:
            player.rect.left = 101
    elif move_right:
        player.rect.x += 4
        if player.rect.right > 389:
            player.rect.right = 389

    player_group.draw(screen)


    if len(vehicle_group) < 2:
        if all(v.rect.top >= v.rect.height * 1.5 for v in vehicle_group):
            lane = random.choice(lanes)
            name, img = random.choice(vehicle_imagenes)
            vehicle_group.add(Vehicle(img, lane, -100, name=name))


    for v in vehicle_group:
        v.rect.y += speed
        v.update()
        if v.rect.top >= height:
            v.kill()
            puntaje += 1
            if puntaje > max_puntaje:
                max_puntaje = puntaje
                nombre_max = nombre_conductor
            if puntaje % 5 == 0:
                speed += 1

    vehicle_group.draw(screen)


    font = pygame.font.Font(pygame.font.get_default_font(), 16)
    color = white if not noche else (220, 220, 220)
    screen.blit(font.render(f'Puntaje: {puntaje}', True, color), (30, 400))
    screen.blit(font.render(f'Máx: {max_puntaje} - {nombre_max}', True, color), (width - 200, 10))
    mostrar_nombre(nombre_conductor, noche)


    if pygame.sprite.spritecollide(player, vehicle_group, True):
        gameover = True
        crash_rect.center = [player.rect.center[0], player.rect.top]

    if gameover:
        screen.blit(crash, crash_rect)
        pygame.draw.rect(screen, red, (0, 50, width, 120))
        f_game = pygame.font.Font(pygame.font.get_default_font(), 20)
        screen.blit(f_game.render('¡Perdiste!', True, white), (width // 2 - 50, 80))
        screen.blit(f_game.render('¿Deseas volver a jugar? (Y/N)', True, white), (width // 2 - 130, 120))

    pygame.display.update()


    while gameover:
        clock.tick(fps)
        for event in pygame.event.get():
            if event.type == QUIT:
                gameover = False
                running = False
            if event.type == KEYDOWN:
                if event.key == K_y:
                    gameover = False
                    speed = 2
                    puntaje = 0
                    vehicle_group.empty()
                    player.rect.center = [player_x, player_y]
                    move_left = move_right = False
                    nombre_conductor = pedir_nombre() 
                elif event.key == K_n:
                    gameover = False
                    running = False

pygame.quit()
