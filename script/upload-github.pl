#!/usr/bin/perl

use strict;
use warnings;

# Verifica que haya un argumento
if (@ARGV < 1) {
    die "Uso: $0 'mensaje del commit'\n";
}

my $message = $ARGV[0];

# Añadir todos los cambios
system("git add .") == 0
    or die "Error al hacer git add\n";

# Hacer commit
system("git", "commit", "-m", $message) == 0
    or die "Error al hacer git commit\n";

# Push a la rama main
system("git push", "origin", "main") == 0
    or die "Error al hacer git push\n";

print "Commit y push realizados correctamente.\n";
